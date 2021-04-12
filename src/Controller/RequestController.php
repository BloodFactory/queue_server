<?php

namespace App\Controller;

use App\Entity\Appointment;
use App\Entity\Organization;
use App\Entity\OrganizationService;
use App\Entity\Registration;
use DateInterval;
use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

/**
 * @Route("/requests")
 */
class RequestController extends AbstractController
{
    /**
     * @Route("/step1")
     * @return Response
     */
    public function step1(): Response
    {
//        $organizations = $this->getDoctrine()->getRepository(Organization::class)->findAll();

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

        dd ($organizations);

        $response = [];

        foreach ($organizations as $organization) {
            $response[] = [
                'value' => $organization->getId(),
                'label' => $organization->getName()
            ];
        }

        return $this->json($response);
    }

    /**
     * @Route("/step2")
     * @param Request $request
     * @return Response
     */
    public function step2(Request $request): Response
    {
        $organizationID = $request->query->getInt('organization', 0);

        if (!$organizationID) return new Response('Неверный формат запроса', Response::HTTP_BAD_REQUEST);

        $organizationServices = $this->getDoctrine()->getRepository(OrganizationService::class)->findBy(['organization' => $organizationID]);

        $response = [];

        foreach ($organizationServices as $organizationService) {
            $service = $organizationService->getService();
            $response[] = [
                'value' => $service->getId(),
                'label' => $service->getName()
            ];
        }

        return $this->json($response);
    }

    /**
     * @Route("/step3", methods={"GET"})
     * @param Request $request
     * @return Response
     */
    public function step3(Request $request): Response
    {
        $organizationServiceID = $request->query->getInt('service');

        if (!$organizationServiceID) return new Response('Неверный формат запроса', Response::HTTP_BAD_REQUEST);

        $appointments = $this->getDoctrine()
                             ->getRepository(Appointment::class)
                             ->createQueryBuilder('appointment')
                             ->andWhere('appointment.organizationService = :organizationService')
                             ->andWhere('appointment.date >= :date')
                             ->setParameter('organizationService', $organizationServiceID)
                             ->setParameter('date', new DateTime())
                             ->getQuery()
                             ->getResult();

        $response = [];

        foreach ($appointments as $appointment) {
            $response[] = [
                'value' => $appointment->getDate()->format('d.m.Y')
            ];
        }

        return $this->json($response);
    }

    /**
     * @Route("/step4", methods={"GET"})
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function step4(Request $request): Response
    {
        $organizationServiceID = $request->query->getInt('service');
        $day = $request->query->get('day');

        try {
            $day = new DateTime($day);
        } catch (Throwable $e) {
            return new Response('Неверный формат запроса', Response::HTTP_BAD_REQUEST);
        }

        try {
            /** @var ?Appointment $appointment */
            $appointment = $this->getDoctrine()
                                ->getRepository(Appointment::class)
                                ->createQueryBuilder('appointment')
                                ->andWhere('appointment.organizationService = :organizationService')
                                ->andWhere('appointment.date = :date')
                                ->setParameter('organizationService', $organizationServiceID)
                                ->setParameter('date', $day)
                                ->getQuery()
                                ->getOneOrNullResult();
        } catch (Throwable $e) {
            return new Response('Неверный формат запроса', Response::HTTP_BAD_REQUEST);
        }

        $response = [];

        if (!$appointment) {
            return $this->json($response);
        }

        /** @var DateTime $timeFrom */
        $timeFrom = $appointment->getTimeFrom();
        $timeTill = $appointment->getTimeTill();
        $duration = $appointment->getDuration();
        $needDinner = $appointment->getNeedDinner();
        $dinnerFrom = $appointment->getDinnerFrom();
        $dinnerTill = $appointment->getDinnerTill();

        $time = clone $timeFrom;

        do {
            if ($needDinner && $time >= $dinnerFrom && $time < $dinnerTill) continue;

            $response[] = [
                'value' => $time->format('H:i')
            ];
        } while ($timeTill > $time->add(new DateInterval('PT' . $duration . 'M')));

        return $this->json($response);
    }

    /**
     * @Route("/registrate", methods={"POST"})
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
                            ->getOneOrNullResult();;

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
}
