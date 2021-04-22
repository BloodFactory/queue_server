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

            $diff = $organization->getTimezone() - 3;

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
     * @Route("", methods={"POST"}, name="registrate")
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function reg(Request $request): Response
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

        /** @var Registration[] $reg */
        $reg = $this->getDoctrine()
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
            $reg->andWhere('registration.middleName = :firstName')->setParameter('middleName', $data['middleName']);
        }

        $reg = $reg->getQuery()
                   ->getResult();

//        dd($reg);

        if ($reg) {
            $d = $reg[0]->getAppointment()->getDate()->format('d.m.Y') . ' ' . $reg[0]->getTime()->format('H:i');
            return new Response("Вы уже записаны на ${d}", Response::HTTP_CONFLICT);
        }

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
     * @Route("/{id}", methods={"DELETE"}, name="delete")
     * @IsGranted("ROLE_CLIENT")
     * @param int $id
     * @param Request $request
     * @return Response
     */
    public function delete(int $id, Request $request): Response
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
}
