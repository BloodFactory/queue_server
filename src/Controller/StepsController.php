<?php

namespace App\Controller;

use App\Entity\Organization;
use App\Entity\Service;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/steps")
 * Class StepsController
 * @package App\Controller
 */
class StepsController extends AbstractController
{
    /**
     * @Route("/organizations", methods={"GET"})
     * @return JsonResponse
     */
    public function organizations(): JsonResponse
    {
        $organizations = $this->getDoctrine()->getRepository(Organization::class)->fetchDictionary();

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
     * @Route("/services", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function services(Request $request): JsonResponse
    {
        $organizationID = $request->query->getInt('organization', 0);

        if (!$organizationID) return $this->json([], Response::HTTP_BAD_REQUEST);

        $qb = $this->getDoctrine()->getRepository(Service::class)->createQueryBuilder('s')
                   ->andWhere('s.organization = :org')
                   ->setParameter('org', $organizationID);

        $services = $qb->getQuery()->getResult();

        $response = [];

        foreach ($services as $service) {
            $response[] = [
                'value' => $service->getId(),
                'label' => $service->getName()
            ];
        }

        return $this->json($response);
    }

    /**
     * @Route("/days", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function days(Request $request): JsonResponse
    {
        $serviceID = $request->query->getInt('service', 0);

        if (!$serviceID) return $this->json([], Response::HTTP_BAD_REQUEST);

        $qb = $this->getDoctrine()->getRepository(Service::class)->createQueryBuilder('s')
                   ->select('s.days')
                   ->andWhere('s.id = :serv')
                   ->setParameter('serv', $serviceID);

        $days = $qb->getQuery()->getSingleResult();

        return $this->json($days);
    }
}
