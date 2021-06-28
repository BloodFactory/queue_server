<?php

namespace App\Controller;

use App\Entity\Appointment;
use App\Entity\Organization;
use App\Entity\OrganizationService;
use App\Entity\Registration;
use DateInterval;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Swift_Mailer;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

/**
 * @Route("/requests", name="requests_")
 */
class RequestController extends AbstractController
{
    /**
     * @Route("", methods={"GET"}, name="fetch")
     * @return Response
     * @throws Exception
     */
    public function fetch(): Response
    {
        $organizations = $this->getDoctrine()
                              ->getRepository(Organization::class)
                              ->createQueryBuilder('organization')
                              ->addSelect('appointments')
                              ->addSelect('registrations')
                              ->addSelect('service')
                              ->innerJoin('organization.appointments', 'appointments')
                              ->leftJoin('appointments.registrations', 'registrations')
                              ->leftJoin('appointments.service', 'service')
                              ->andWhere('appointments.date >= :date')
                              ->setParameter('date', new DateTime())
                              ->getQuery()
                              ->getResult();

        $response = [];

        $_now = new DateTimeImmutable();

        /** @var Organization $organization */
        foreach ($organizations as $organization) {
            $now = DateTime::createFromImmutable($_now);

            $diff = $organization->getTimezone();

            if ($diff > 0) {
                $now->add(new DateInterval("PT{$diff}H"));
            } elseif ($diff < 0) {
                $diff = abs($diff);
                $now->sub(new DateInterval("PT{$diff}H"));
            }

            $itemOrganization = [
                'value' => $organization->getId(),
                'label' => $organization->getName()
            ];


            foreach ($organization->getAppointments() as $appointment) {
                $service = $appointment->getService();
                $itemService = [
                    'value' => $service->getId(),
                    'label' => $service->getName()
                ];


                $timeFrom = $appointment->getTimeFrom();
                $timeTill = $appointment->getTimeTill();
                $duration = $appointment->getDuration();
                $needDinner = $appointment->getNeedDinner();
                $dinnerFrom = $appointment->getDinnerFrom();
                $dinnerTill = $appointment->getDinnerTill();
                $persons = $appointment->getPersons();

                /** @var DateTime $day */
                $day = clone $appointment->getDate();

                $time = clone $timeFrom;

                $itemAppointment = [
                    'id' => $appointment->getId(),
                    'date' => $appointment->getDate()->format('d.m.Y'),
                    'duration' => $duration,
                    'persons' => $persons
                ];

                $registrationTimes = [];

                foreach ($appointment->getRegistrations() as $registration) {
                    $registrationTime = $registration->getTime()->format('H:i');
                    $registrationTimes[$registrationTime] = empty($registrationTimes[$registrationTime]) ? 1 : $registrationTimes[$registrationTime] + 1;
                }

                do {
                    if ($needDinner && $time >= $dinnerFrom && $time < $dinnerTill) continue;

                    $day->setTime((int)$time->format('H'), (int)$time->format('i'));
                    $timeStr = $time->format('H:i');

                    $itemTime = [
                        'value' => $timeStr,
                        'free' => ($day > $now) && (empty($registrationTimes[$timeStr]) || $registrationTimes[$timeStr] < $persons)
                    ];

                    $itemAppointment['times'][] = $itemTime;
                } while ($timeTill > $time->add(new DateInterval('PT' . $duration . 'M')));

                $itemService['appointments'][] = $itemAppointment;


                $itemOrganization['services'][] = $itemService;
            }

            $response[] = $itemOrganization;
        }

        return $this->json($response);
    }


    /**
     * @Route("", methods={"POST"}, name="registrate")
     * @param Request $request
     * @param Swift_Mailer $mailer
     * @return Response
     * @throws NonUniqueResultException
     * @throws NoResultException
     * @throws Exception
     */
    public function reg(Request $request, Swift_Mailer $mailer): Response
    {
        $data = json_decode($request->getContent(), true);

        $registrationTime = new DateTime($data['time']);

        /** @var Appointment|null $appointment */
        $appointment = $this->getDoctrine()
                            ->getRepository(Appointment::class)
                            ->createQueryBuilder('appointment')
                            ->andWhere('appointment.service = :service')
                            ->andWhere('appointment.date = :date')
                            ->setParameter('service', $data['service'])
                            ->setParameter('date', new DateTime($data['date']))
                            ->getQuery()
                            ->getOneOrNullResult();

        $qb = $this->getDoctrine()
                   ->getRepository(Registration::class)
                   ->createQueryBuilder('registration')
                   ->addSelect('appointment')
                   ->leftJoin('registration.appointment', 'appointment')
                   ->andWhere('appointment.date >= :date')
                   ->andWhere('registration.lastName = :lastName')
                   ->andWhere('registration.firstName = :firstName')
                   ->andWhere('registration.birthday = :birthday')
                   ->setParameter('date', new DateTime($data['date']))
                   ->setParameter('lastName', $data['lastName'])
                   ->setParameter('firstName', $data['firstName'])
                   ->setParameter('birthday', new DateTime($data['birthday']));

        if (!empty($data['middleName'])) {
            $qb->andWhere('registration.middleName = :middleName')->setParameter('middleName', $data['middleName']);
        }

        $userRegistrations = $qb->getQuery()
                                ->getResult();

        if ($userRegistrations) {
            $d = $userRegistrations[0]->getAppointment()->getDate()->format('d.m.Y') . ' ' . $userRegistrations[0]->getTime()->format('H:i');
            return new Response("Вы уже записаны на ${d}", Response::HTTP_CONFLICT);
        }

        $totalRegistrations = $this->getDoctrine()
                                   ->getRepository(Registration::class)
                                   ->createQueryBuilder('registration')
                                   ->select('count(registration.id)')
                                   ->andWhere('registration.time = :time')
                                   ->andWhere('registration.appointment = :appointment')
                                   ->setParameter('time', $registrationTime)
                                   ->setParameter('appointment', $appointment->getId())
                                   ->getQuery()->getSingleScalarResult();

        if ($totalRegistrations >= $appointment->getPersons()) {
            return new Response("На данное время больше нет вакантных мест", Response::HTTP_CONFLICT);
        }

        $registration = new Registration();

        $registration->setAppointment($appointment)
                     ->setBirthday(new DateTime($data['birthday']))
                     ->setLastName($data['lastName'])
                     ->setFirstName($data['firstName'])
                     ->setTime($registrationTime);

        if (!empty($data['middleName'])) {
            $registration->setMiddleName($data['middleName']);
        }

        if (!empty($data['email'])) {
            $registration->setEmail($data['email']);
        }

        if (!empty($data['phone'])) {
            $registration->setPhone($data['phone']);
        }

        $em = $this->getDoctrine()->getManager();

        $em->persist($registration);
        $em->flush();

        if (!empty($data['email'])) {
            $message = (new Swift_Message('Подтверждение заявки на приём'))
                ->setFrom('noreplay@marinet.ru')
                ->setTo($data['email'])
                ->setBody(
                    $this->renderView(
                        'mail/success_registration.html.twig',
                        [
                            'fio' => implode(' ', [
                                $data['lastName'],
                                $data['firstName'],
                                empty($data['middleName']) ? '' : $data['middleName']
                            ]),
                            'birthday' => $data['birthday'],
                            'organization' => $appointment->getOrganization()->getName(),
                            'service' => $appointment->getService()->getName(),
                            'date' => $data['date'],
                            'time' => $data['time']
                        ]
                    ),
                    'text/html'
                );

            if (!$mailer->send($message)) {
                return new Response();
            }
        }

        return new Response();
    }

    /**
     * @Route("/{id}", methods={"DELETE"}, name="delete")
     * @IsGranted("ROLE_CLIENT")
     * @param int $id
     * @return Response
     */
    public function delete(int $id): Response
    {
        $registration = $this->getDoctrine()->getRepository(Registration::class)->find($id);

        if (!$registration) {
            return new Response('Заявка не найдена', Response::HTTP_NOT_FOUND);
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($registration);
        $em->flush();

        return new Response();
    }

    /**
     * @Route("/search", name="search")
     * @param Request $request
     * @return Response
     */
    public function search(Request $request): Response
    {
        $query = $request->query;

        $lastName = $query->get('lastName', '');
        $firstName = $query->get('firstName', '');
        $middleName = $query->get('middleName', '');
        $birthday = $query->get('birthday', '');

        if (!$lastName) return new Response('Неверный формат запроса', Response::HTTP_BAD_REQUEST);
        if (!$firstName) return new Response('Неверный формат запроса', Response::HTTP_BAD_REQUEST);
        if (!$birthday) return new Response('Неверный формат запроса', Response::HTTP_BAD_REQUEST);

        try {
            $_birthday = new DateTime($birthday);

            if ($birthday !== $_birthday->format('d.m.Y')) throw new Exception();

            $birthday = $_birthday;
        } catch (Throwable $e) {
            return new Response('Неверный формат запроса', Response::HTTP_BAD_REQUEST);
        }

        $now = new DateTime();

        $qb = $this->getDoctrine()
                   ->getRepository(Registration::class)
                   ->createQueryBuilder('registration')
                   ->addSelect('appointment')
                   ->leftJoin('registration.appointment', 'appointment')
                   ->leftJoin('appointment.organizationService', 'organizationService')
                   ->leftJoin('organizationService.organization', 'organization')
                   ->andWhere('appointment.date = :date')
                   ->andWhere('DATEADD(hour, organization.timezone, registration.time) > :date')
                   ->orWhere('appointment.date > :date')
                   ->andWhere('registration.lastName = :lastName')
                   ->andWhere('registration.firstName = :firstName')
                   ->andWhere('registration.birthday = :birthday')
                   ->setParameter('date', $now)
                   ->setParameter('lastName', $lastName)
                   ->setParameter('firstName', $firstName)
                   ->setParameter('birthday', $birthday);

        if ($middleName) {
            $qb->andWhere('registration.middleName = :middleName')
               ->setParameter('middleName', $middleName);
        }

        try {
            /** @var ?Registration $registration */
            $registration = $qb->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return new Response('По данному запросу найдено более обной записи', Response::HTTP_BAD_REQUEST);
        }

        if (!$registration) {
            return new Response('По данному запросу ничего не найдено', Response::HTTP_BAD_REQUEST);
        }

        $date = $registration->getAppointment()->getDate()->format('d.m.Y');
        $time = $registration->getTime()->format('H:i');

        return new Response("Вы записаны ${date} на ${time}");
    }
}
