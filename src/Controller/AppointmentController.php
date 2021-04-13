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

/**
 * @Route("/appointments")
 */
class AppointmentController extends AbstractController
{
    /**
     * @Route("", methods={"POST"})
     * @IsGranted("ROLE_USER")
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

        if (empty($data['organizationService'])) return new Response('Неверный формат запроса1', Response::HTTP_BAD_REQUEST);
        if (empty($data['date'])) return new Response('Неверный формат запроса2', Response::HTTP_BAD_REQUEST);
        if (empty($data['timeFrom'])) return new Response('Неверный формат запроса3', Response::HTTP_BAD_REQUEST);
        if (empty($data['timeTill'])) return new Response('Неверный формат запроса4', Response::HTTP_BAD_REQUEST);
        if (!isset($data['needDinner'])) return new Response('Неверный формат запроса5', Response::HTTP_BAD_REQUEST);
        if (empty($data['duration'])) return new Response('Неверный формат запроса6', Response::HTTP_BAD_REQUEST);
        if (empty($data['persons'])) return new Response('Неверный формат запроса7', Response::HTTP_BAD_REQUEST);

        if ($data['needDinner']) {
            if (empty($data['dinnerFrom'])) return new Response('Неверный формат запроса8', Response::HTTP_BAD_REQUEST);
            if (empty($data['dinnerTill'])) return new Response('Неверный формат запроса9', Response::HTTP_BAD_REQUEST);
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
            return new Response('Неверный формат запроса11', Response::HTTP_BAD_REQUEST);
        }


        if ($id) {
            $organizationServiceAppointment = $this->getDoctrine()->getRepository(Appointment::class)->find($id);
            if (!$organizationServiceAppointment) return new Response('Неверный формат запроса', Response::HTTP_NOT_FOUND);
        } else {
            $organizationServiceAppointment = new Appointment();
        }

        try {
            $organizationServiceAppointment->setOrganizationService($organizationService)
                                           ->setDate($date)
                                           ->setTimeFrom($timeFrom)
                                           ->setTimeTill($timeTill)
                                           ->setNeedDinner($data['needDinner'])
                                           ->setDinnerFrom($dinnerFrom)
                                           ->setDinnerTill($dinnerTill)
                                           ->setDuration((int)$data['duration'])
                                           ->setPersons((int)$data['persons']);
        } catch (\Throwable $e) {
            return new Response('Неверный формат запроса12', Response::HTTP_BAD_REQUEST);
        }

        $em = $this->getDoctrine()->getManager();

        $em->persist($organizationServiceAppointment);

        try {
            $em->flush();
        } catch (\Throwable $e) {
            return new Response('Неудалсоь выполнить запрос13', Response::HTTP_BAD_REQUEST);
        }

        return new Response();
    }

    /**
     * @Route("", methods={"GET"})
     * @IsGranted("ROLE_USER")
     * @param Request $request
     * @return Response
     */
    public function fetchList(Request $request): Response
    {
        $criteria = [];

        if ($organizationService = $request->query->getInt('organizationService', 0)) {
            $criteria['organizationService'] = $organizationService;
        }

        $appointments = $this->getDoctrine()->getRepository(Appointment::class)->findBy($criteria);

        $response = [];

        foreach ($appointments as $appointment) {
            $response[] = $this->convertDataToArray($appointment);
        }

        return $this->json($response);
    }

    private function convertDataToArray(Appointment $appointment): array
    {
        return [
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
    }

    /**
     * @Route("/{id}", methods={"GET"})
     * @IsGranted("ROLE_USER")
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
     * @IsGranted("ROLE_USER")
     * @param int $id
     * @param Request $request
     * @return Response
     */
    public function update(int $id, Request $request): Response
    {
        return $this->save($request, $id);
    }
}
