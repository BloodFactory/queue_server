<?php

namespace App\Controller;

use App\Entity\Organization;
use App\Entity\Service;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/dictionary", name="dictionary_")
 */
class DictionaryController extends AbstractController
{
    /**
     * @Route("/organizations", methods={"GET"}, name="orgainzations")
     * @param Request $request
     * @return Response
     */
    public function fetchOrganizations(Request $request): Response
    {
        $filter = $request->query->get('filter');
        $parent = $request->query->getInt('parent');

        $qb = $this->getDoctrine()
                   ->getRepository(Organization::class)
                   ->createQueryBuilder('organization');

        if ($filter) {
            $qb->andWhere('organization.name LIKE :filter')
               ->setParameter('filter', "%{$filter}%");
        }

        if ($parent) {
            $qb->andWhere('organization.parent = :parent')
               ->setParameter('parent', $parent);
        } else {
            $qb->andWhere('organization.parent IS NULL');
        }

        $organizations = $qb->getQuery()->getResult();

        $response = [];

        /** @var Organization $organization */
        foreach ($organizations as $organization) {
            $response[] = [
                'value' => $organization->getId(),
                'label' => $organization->getName(),
                'branches' => $organization->getBranches() ? count($organization->getBranches()) : 0
            ];
        }

        return $this->json($response);
    }

    /**
     * @Route("/services", methods={"GET"}, name="services")
     * @return Response
     */
    public function fetchServices(): Response
    {
        $services = $this->getDoctrine()->getRepository(Service::class)->findAll();

        $response = [];

        foreach ($services as $service) {
            $response[] = [
                'value' => $service->getId(),
                'label' => $service->getName()
            ];
        }

        return $this->json($response);
    }
}
