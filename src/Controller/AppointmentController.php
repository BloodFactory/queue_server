<?php

namespace App\Controller;

use App\Entity\Appointment;
use App\Entity\Organization;
use App\Entity\OrganizationService;
use App\Entity\Registration;
use App\Entity\Service;
use App\Entity\User;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function Doctrine\ORM\QueryBuilder;

/**
 * @Route("/appointments", name="appointments_")
 * @IsGranted("ROLE_USER")
 */
class AppointmentController extends AbstractController
{
    /**
     * @Route("", methods={"POST"}, name="add")
     * @param Request $request
     * @return Response
     */
    public function add(Request $request): Response
    {
        return $this->save($request);
    }

    private function save(Request $request, ?int $id = null): Response
    {
        $data = $request->request->all();

        if (empty($data['organization'])) return new Response('Неверный формат запроса', Response::HTTP_BAD_REQUEST);
        if (empty($data['service'])) return new Response('Неверный формат запроса', Response::HTTP_BAD_REQUEST);
        if (empty($data['date'])) return new Response('Неверный формат запроса', Response::HTTP_BAD_REQUEST);
        if (empty($data['timeFrom'])) return new Response('Неверный формат запроса', Response::HTTP_BAD_REQUEST);
        if (empty($data['timeTill'])) return new Response('Неверный формат запроса', Response::HTTP_BAD_REQUEST);
        if (!isset($data['needDinner'])) return new Response('Неверный формат запроса', Response::HTTP_BAD_REQUEST);
        if (empty($data['duration'])) return new Response('Неверный формат запроса', Response::HTTP_BAD_REQUEST);
        if (empty($data['persons'])) return new Response('Неверный формат запроса', Response::HTTP_BAD_REQUEST);

        if ($data['needDinner']) {
            if (empty($data['dinnerFrom'])) return new Response('Неверный формат запроса', Response::HTTP_BAD_REQUEST);
            if (empty($data['dinnerTill'])) return new Response('Неверный формат запроса', Response::HTTP_BAD_REQUEST);
        }

        try {
            $date = new DateTime($data['date']);
            $timeFrom = new DateTime($data['timeFrom']);
            $timeTill = new DateTime($data['timeTill']);
            $dinnerFrom = $data['needDinner'] ? new DateTime($data['dinnerFrom']) : null;
            $dinnerTill = $data['needDinner'] ? new DateTime($data['dinnerTill']) : null;
        } catch (\Throwable $e) {
            return new Response('', Response::HTTP_BAD_REQUEST);
        }

        if (!$organization = $this->getDoctrine()->getRepository(Organization::class)->find($data['organization'])) {
            return new Response('Неверный формат запроса', Response::HTTP_BAD_REQUEST);
        }

        if (!$service = $this->getDoctrine()->getRepository(Service::class)->find($data['service'])) {
            return new Response('Неверный формат запроса', Response::HTTP_BAD_REQUEST);
        }

        if ($id) {
            $appointment = $this->getDoctrine()->getRepository(Appointment::class)->find($id);
            if (!$appointment) return new Response('Неверный формат запроса', Response::HTTP_NOT_FOUND);
        } else {
            $appointment = new Appointment();
        }


        if ($appointment->getRegistrations()->count() > 0) {
            return new Response('Редактирование заявки запрещено, так как имеются записанные кандидаты', Response::HTTP_FORBIDDEN);
        }

        try {
            $appointment->setOrganization($organization)
                        ->setService($service)
                        ->setDate($date)
                        ->setTimeFrom($timeFrom)
                        ->setTimeTill($timeTill)
                        ->setNeedDinner($data['needDinner'])
                        ->setDinnerFrom($dinnerFrom)
                        ->setDinnerTill($dinnerTill)
                        ->setDuration((int)$data['duration'])
                        ->setPersons((int)$data['persons']);
        } catch (\Throwable $e) {
            return new Response('Неверный формат запроса', Response::HTTP_BAD_REQUEST);
        }

        $em = $this->getDoctrine()->getManager();

        $em->persist($appointment);

        try {
            $em->flush();
        } catch (\Throwable $e) {
            return new Response('Неудалсоь выполнить запрос', Response::HTTP_BAD_REQUEST);
        }

        return new Response();
    }

    /**
     * @Route("", methods={"GET"}, name="fetch_list")
     * @param Request $request
     * @return Response
     */
    public function fetchList(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $response = $this->getDoctrine()
                         ->getRepository(Organization::class)
                         ->createQueryBuilder('o')
                         ->addSelect('a')
                         ->addSelect('s')
                         ->addSelect('r')
                         ->leftJoin('o.appointments', 'a')
                         ->leftJoin('a.service', 's')
                         ->leftJoin('a.registrations', 'r')
                         ->leftJoin('o.userRights', 'ur')
                         ->andWhere('ur.user = :user')
                         ->andWhere('ur.view = 1')
                         ->andWhere('a.date >= :now')
                         ->setParameter('user', $user->getId())
                         ->setParameter('now', new DateTime())
                         ->getQuery()
                         ->getResult();

        $result = [];

        /** @var Organization $org */
        foreach ($response as $org) {
            $organization = [
                'id' => $org->getId(),
                'name' => $org->getName(),
                'timezone' => $org->getTimezone()
            ];

            $services = [];
            $servicesLinks = [];

            /** @var Appointment $app */
            foreach ($org->getAppointments() as $app) {
                $serv = $app->getService();

                if (!isset($servicesLinks[$serv->getId()])) {
                    $service = [
                        'id' => $serv->getId(),
                        'name' => $serv->getName()
                    ];

                    $servicesLinks[$serv->getId()] = &$service;
                    $services[] = &$service;
                } else {
                    $service = $servicesLinks[$serv->getId()];
                }

                $registrations = [];

                /** @var Registration $reg */
                foreach ($app->getRegistrations() as $reg) {
                    $registrations[] = [
                        'id' => $reg->getId(),
                        'lastName' => $reg->getLastName(),
                        'firstName' => $reg->getFirstName(),
                        'middleName' => $reg->getMiddleName() ?? '',
                        'birthday' => $reg->getBirthday()->format('d.m.Y'),
                        'time' => $reg->getTime()->format('H:i'),
                        'email' => $reg->getEmail(),
                        'phone' => $reg->getPhone()
                    ];
                }

                $appointment = [
                    'id' => $app->getId(),
                    'date' => $app->getDate()->format('d.m.Y'),
                    'timeFrom' => $app->getTimeFrom()->format('H:i'),
                    'timeTill' => $app->getTimeTill()->format('H:i'),
                    'needDinner' => $app->getNeedDinner(),
                    'dinnerFrom' => $app->getDinnerFrom() ? $app->getDinnerFrom()->format('H:i') : '',
                    'dinnerTill' => $app->getDinnerTill() ? $app->getDinnerTill()->format('H:i') : '',
                    'duration' => $app->getDuration(),
                    'persons' => $app->getPersons(),
                    'registrations' => $registrations
                ];

                $service['appointments'][] = $appointment;

                unset($service);
            }

            $organization['services'] = $services;

            $result[] = $organization;
        }


        return $this->json($result);
    }

    /**
     * @Route("/{id}", methods={"GET"}, name="fetch")
     * @param int $id
     * @return Response
     */
    public function fetch(int $id): Response
    {
        $appointment = $this->getDoctrine()->getRepository(Appointment::class)->find($id);

        if (!$appointment) {
            return new Response('', Response::HTTP_NOT_FOUND);
        }

        return $this->json($this->convertDataToArray($appointment));
    }

    private function convertDataToArray(Appointment $appointment): array
    {
        $result = [
            'id' => $appointment->getId(),
            'date' => $appointment->getDate()->format('d.m.Y'),
            'timeFrom' => $appointment->getTimeFrom()->format('H:i'),
            'timeTill' => $appointment->getTimeTill()->format('H:i'),
            'needDinner' => $appointment->getNeedDinner(),
            'dinnerFrom' => $appointment->getDinnerFrom()->format('H:i'),
            'dinnerTill' => $appointment->getDinnerTill()->format('H:i'),
            'duration' => $appointment->getDuration(),
            'persons' => $appointment->getPersons()
        ];

        foreach ($appointment->getRegistrations() as $registration) {
            $result['registrations'][] = [
                'id' => $registration->getId(),
                'time' => $registration->getTime()->format('H:i'),
                'person' => [
                    'lastName' => $registration->getLastName(),
                    'firstName' => $registration->getFirstName(),
                    'middleName' => $registration->getMiddleName() ?? '',
                    'birthday' => $registration->getBirthday()->format('d.m.Y'),
                    'phone' => $registration->getPhone(),
                    'email' => $registration->getEmail()
                ]
            ];
        }

        return $result;
    }

    /**
     * @Route("/{id}", methods={"POST"}, name="update")
     * @param int $id
     * @param Request $request
     * @return Response
     */
    public function update(int $id, Request $request): Response
    {
        return $this->save($request, $id);
    }

    /**
     * @Route("/{id}", methods={"DELETE"}, name="delete")
     * @param int $id
     * @return Response
     */
    public function delete(int $id): Response
    {
        $appointment = $this->getDoctrine()->getRepository(Appointment::class)->find($id);

        if (!$appointment) {
            return new Response('Заявка на приём не найдена', Response:: HTTP_NOT_FOUND);
        }

        if ($appointment->getRegistrations()->count() > 0) {
            return new Response('Удаление записи на приём невозможно, так как имеются записанные кандидаты', Response::HTTP_FORBIDDEN);
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($appointment);
        $em->flush();

        return new  Response();
    }
}
