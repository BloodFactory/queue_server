<?php

namespace App\Controller;

use App\Entity\Organization;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/services")
 * Class ServicesController
 * @package App\Controller
 */
class ServicesController extends AbstractController
{
    /**
     * @Route("", methods={"GET"})
     */
    public function fetchList(): JsonResponse
    {
        $services = $this->getDoctrine()->getRepository(Organization::class)->findBy([], ['id' => 'asc']);

        $response = [];

        foreach ($organizations as $index => $organization) {
            $response[] = [
                'id' => $organization->getId(),
                'index' => $index + 1,
                'name' => $organization->getName()
            ];
        }

        return $this->json($response);
    }
}
