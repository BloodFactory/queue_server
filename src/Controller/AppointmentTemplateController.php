<?php

namespace App\Controller;

use App\Entity\AppointmentTemplate;
use App\Entity\Organization;
use App\Entity\Service;
use App\Entity\User;
use DateTime;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/appointmentTemplates", name="appointment_templates_")
 */
class AppointmentTemplateController extends AbstractController
{
    /**
     * @Route("", name="list", methods={"GET"})
     * @IsGranted("ROLE_CLIENT")
     * @return Response
     */
    public function list(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $templates = $this->getDoctrine()
                          ->getRepository(AppointmentTemplate::class)
                          ->createQueryBuilder('at')
                          ->addSelect('o')
                          ->addSelect('s')
                          ->leftJoin('at.organization', 'o')
                          ->leftJoin('at.service', 's')
                          ->leftJoin('o.userRights', 'ur')
                          ->andWhere('ur.user = :user')
                          ->andWhere('ur.view = 1')
                          ->setParameter('user', $user->getId())
                          ->addOrderBy('o.name')
                          ->addOrderBy('s.name')
                          ->getQuery()
                          ->getResult();

        $result = [];

        $organizations = [];

        /** @var AppointmentTemplate $template */
        foreach ($templates as $template) {
            $organization = $template->getOrganization();
            $service = $template->getService();

            if (!isset($organizations[$organization->getId()])) {
                $org = [
                    'id' => $organization->getId(),
                    'name' => $organization->getName(),
                    'timezone' => $organization->getTimezone()
                ];

                $result[] = &$org;

                $organizations[$organization->getId()] = &$org;
            } else {
                $org = $organizations[$organization->getId()];
            }

            $org['templates'][] = [
                'id' => $template->getId(),
                'service' => [
                    'id' => $service->getId(),
                    'name' => $service->getName()
                ],
                'timeFrom' => $template->getTimeFrom()->format('H:i'),
                'timeTill' => $template->getTimeTill()->format('H:i'),
                'needDinner' => $template->getNeedDinner(),
                'dinnerFrom' => $template->getDinnerFrom() ? $template->getDinnerFrom()->format('H:i') : '',
                'dinnerTill' => $template->getDinnerTill() ? $template->getDinnerTill()->format('H:i') : '',
                'persons' => $template->getPersons(),
                'duration' => $template->getDuration()
            ];
        }

        return $this->json($result);
    }

    /**
     * @Route("", name="add", methods={"POST"})
     * @throws Exception
     */
    public function add(Request $request): Response
    {
        $data = $request->request->all();

        $em = $this->getDoctrine()->getManager();

        foreach ($data['organizations'] as $organization) {
            $org = $this->getDoctrine()->getRepository(Organization::class)->find($organization);

            foreach ($data['services'] as $service) {
                $serv = $this->getDoctrine()->getRepository(Service::class)->find($service);

                if ($this->getDoctrine()->getRepository(AppointmentTemplate::class)->findBy(['service' => $serv->getId(), 'organization' => $org->getId()])) {
                    $organizationName = $org->getName();
                    $serviceName = $serv->getName();

                    return new Response("В системе уже имеется шаблон для организации \"${organizationName}\" и услуги \"${serviceName}\"", Response::HTTP_BAD_REQUEST);
                }

                $appointmentTemplate = new AppointmentTemplate();

                $appointmentTemplate->setOrganization($org)
                                    ->setService($serv)
                                    ->setTimeFrom(new DateTime($data['timeFrom']))
                                    ->setTimeTill(new DateTime($data['timeTill']))
                                    ->setPersons($data['persons'])
                                    ->setDuration($data['duration'])
                                    ->setNeedDinner(isset($data['needDinner']) && '1' === $data['needDinner']);

                if (isset($data['needDinner']) && '1' === $data['needDinner']) {
                    $appointmentTemplate->setDinnerFrom(new DateTime($data['dinnerFrom']))
                                        ->setDinnerTill(new DateTime($data['dinnerTill']));
                }

                $em->persist($appointmentTemplate);
            }
        }

        $em->flush();

        return new Response();
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     * @IsGranted("ROLE_CLIENT")
     * @param int $id
     * @return Response
     */
    public function delete(int $id): Response
    {
        if (!$template = $this->getDoctrine()->getRepository(AppointmentTemplate::class)->find($id)) {
            return new Response('', Response::HTTP_NOT_FOUND);
        }

        $em = $this->getDoctrine()->getManager();

        $em->remove($template);
        $em->flush();

        return new Response();
    }

    /**
     * @Route("/{id}", name="update", methods={"POST"})
     * @IsGranted("ROLE_CLIENT")
     * @param int $id
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function update(int $id, Request $request): Response
    {
        if (!$template = $this->getDoctrine()->getRepository(AppointmentTemplate::class)->find($id)) {
            return new Response('', Response::HTTP_NOT_FOUND);
        }

        $data = $request->request->all();

        if (empty($data['timeFrom'])) return new Response('Неверный формат запроса', Response::HTTP_NOT_FOUND);
        if (empty($data['timeTill'])) return new Response('Неверный формат запроса', Response::HTTP_NOT_FOUND);
        if (empty($data['duration'])) return new Response('Неверный формат запроса', Response::HTTP_NOT_FOUND);
        if (empty($data['persons'])) return new Response('Неверный формат запроса', Response::HTTP_NOT_FOUND);
        if (empty($data['service'])) return new Response('Неверный формат запроса', Response::HTTP_NOT_FOUND);
        if (!$service = $this->getDoctrine()->getRepository(Service::class)->find((int)$data['service'])) return new Response('Неверный формат запроса', Response::HTTP_NOT_FOUND);

        $template->setService($service)
                 ->setTimeFrom(new DateTime($data['timeFrom']))
                 ->setTimeTill(new DateTime($data['timeTill']))
                 ->setDuration((int)$data['duration'])
                 ->setPersons((int)$data['persons']);

        if (!empty($data['needDinner'])) {
            if (empty($data['dinnerFrom'])) return new Response('Неверный формат запроса', Response::HTTP_NOT_FOUND);
            if (empty($data['dinnerTill'])) return new Response('Неверный формат запроса', Response::HTTP_NOT_FOUND);

            $template->setNeedDinner(true)
                     ->setDinnerFrom(new DateTime($data['dinnerFrom']))
                     ->setDinnerTill(new DateTime($data['dinnerTill']));

        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($template);
        $em->flush();

        return new Response();
    }
}
