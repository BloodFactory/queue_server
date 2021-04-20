<?php

namespace App\Controller;

use App\Entity\Appointment;
use App\Entity\OrganizationService;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function Doctrine\ORM\QueryBuilder;

/**
 * @Route("/appointments")
 * @IsGranted("ROLE_USER")
 */
class AppointmentController extends AbstractController
{
    /**
     * @Route("", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function add(Request $request): Response
    {
        return $this->save($request);
    }

    private function save(Request $request, ?int $id = null): Response
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['organizationService'])) return new Response('Неверный формат запроса', Response::HTTP_BAD_REQUEST);
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

        if (!$organizationService = $this->getDoctrine()->getRepository(OrganizationService::class)->find($data['organizationService'])) {
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
            $appointment->setOrganizationService($organizationService)
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
     * @Route("", methods={"GET"})
     * @param Request $request
     * @return Response
     */
    public function fetchList(Request $request): Response
    {
        if (!$organizationService = $request->query->getInt('organizationService', 0)) {
            return new Response('', Response::HTTP_BAD_REQUEST);
        }

        $appointments = $this->getDoctrine()
                             ->getRepository(Appointment::class)
                             ->createQueryBuilder('appointment')
                             ->addSelect('registrations')
                             ->leftJoin('appointment.registrations', 'registrations')
                             ->andWhere('appointment.organizationService = :organizationService')
                             ->andWhere('appointment.date >= :date')
                             ->setParameter('organizationService', $organizationService)
                             ->setParameter('date', new DateTime())
                             ->addOrderBy('appointment.date')
                             ->addOrderBy('registrations.time')
                             ->getQuery()
                             ->getResult();

        $response = [];

        foreach ($appointments as $appointment) {
            $response[] = $this->convertDataToArray($appointment);
        }

        return $this->json($response);
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
                'status' => $registration->getStatus(),
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
     * @Route("/{id}", methods={"GET"})
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

    /**
     * @Route("/{id}", methods={"POST"})
     * @param int $id
     * @param Request $request
     * @return Response
     */
    public function update(int $id, Request $request): Response
    {
        return $this->save($request, $id);
    }

    /**
     * @Route("/{id}", methods={"DELETE"})
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
            return new Response('Удаление заявки запрещено, так как имеются записанные кандидаты', Response::HTTP_FORBIDDEN);
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($appointment);
        $em->flush();

        return new  Response();
    }
}
