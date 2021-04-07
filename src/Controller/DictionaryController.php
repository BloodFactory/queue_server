<?php

namespace App\Controller;

use App\Entity\Organization;
use App\Entity\Service;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/dictionary")
 */
class DictionaryController extends AbstractController
{
    /**
     * @Route("/organizations", methods={"GET"})
     * @return Response
     */
    public function fetchOrganizations(): Response
    {
        $organizations = $this->getDoctrine()->getRepository(Organization::class)->findAll();

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
