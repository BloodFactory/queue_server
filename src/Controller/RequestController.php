<?php

namespace App\Controller;

use App\Entity\Appointment;
use App\Entity\Organization;
use App\Entity\OrganizationService;
use App\Entity\Registration;
use DateInterval;
use DateTime;
use DateTimeImmutable;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/requests")
 */
class RequestController extends AbstractController
{
    /**
     * @Route("", methods={"GET"})
     * @return Response
     * @throws Exception
     */
    public function fetch(): Response
    {
        $organizations = $this->getDoctrine()
                              ->getRepository(Organization::class)
                              ->createQueryBuilder('organization')
                              ->addSelect('organizationServices')
                              ->addSelect('appointments')
                              ->addSelect('registrations')
                              ->innerJoin('organization.organizationServices', 'organizationServices')
                              ->innerJoin('organizationServices.appointments', 'appointments')
                              ->leftJoin('appointments.registrations', 'registrations')
                              ->andWhere('appointments.date >= :date')
                              ->setParameter('date', new DateTime())
                              ->getQuery()
                              ->getResult();

        $response = [];

        $_now = new DateTimeImmutable();

        /** @var Organization $organization */
        foreach ($organizations as $organization) {
            $now = DateTime::createFromImmutable($_now);
            $now->setTime((int)$now->format('H') + $organization->getTimezone() - 3, (int)$now->format('i'));

            $itemOrganization = [
                'value' => $organization->getId(),
                'label' => $organization->getName()
            ];

            /** @var OrganizationService $organizationService */
            foreach ($organization->getOrganizationServices() as $organizationService) {
                $itemService = [
                    'value' => $organizationService->getId(),
                    'label' => $organizationService->getService()->getName()
                ];

                /** @var Appointment $appointment */
                foreach ($organizationService->getAppointments() as $appointment) {
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
                        if (false === $registration->getStatus()) continue;

                        $registrationTimes[$registration->getTime()->format('H:i')] = empty($registrationTimes[$registration->getTime()->format('H:i')]) ? 1 : $registrationTimes[$registration->getTime()->format('H:i')]++;
                    }

                    do {
                        if ($needDinner && $time >= $dinnerFrom && $time < $dinnerTill) continue;

                        $day->setTime((int)$time->format('H'), (int)$time->format('i'));

                        $itemTime = [
                            'value' => $time->format('H:i'),
                            'free' => $day > $now && (empty($registrationTimes[$time->format('H:i')]) || $registrationTimes[$time->format('H:i')] < $persons)
                        ];

                        $itemAppointment['times'][] = $itemTime;
                    } while ($timeTill > $time->add(new DateInterval('PT' . $duration . 'M')));

                    $itemService['appointments'][] = $itemAppointment;
                }

                $itemOrganization['services'][] = $itemService;
            }

            $response[] = $itemOrganization;
        }

        return $this->json($response);
    }


    /**
     * @Route("", methods={"POST"})
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function registrate(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        $appointment = $this->getDoctrine()
                            ->getRepository(Appointment::class)
                            ->createQueryBuilder('appointment')
                            ->andWhere('appointment.organizationService = :service')
                            ->andWhere('appointment.date = :date')
                            ->setParameter('service', $data['service'])
                            ->setParameter('date', new DateTime($data['date']))
                            ->getQuery()
                            ->getOneOrNullResult();

        $registration = new Registration();

        $registration->setAppointment($appointment)
                     ->setBirthday(new DateTime($data['birthday']))
                     ->setLastName($data['lastName'])
                     ->setFirstName($data['firstName'])
                     ->setTime(new DateTime($data['time']));

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

        return new Response();
    }

    /**
     * @Route("/{id}/status", methods={"POST"})
     * @IsGranted("ROLE_CLIENT")
     * @param int $id
     * @param Request $request
     * @return Response
     */
    public function setStatus(int $id, Request $request): Response
    {
        $registration = $this->getDoctrine()->getRepository(Registration::class)->find($id);

        if (!$registration) {
            return new Response('Заявка не найдена', Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        $registration->setStatus($data['status']);

        $em = $this->getDoctrine()->getManager();
        $em->persist($registration);
        $em->flush();

        return new Response();
    }
}
