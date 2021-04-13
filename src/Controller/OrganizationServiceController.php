<?php

namespace App\Controller;

use App\Entity\OrganizationService;
use App\Entity\Service;
use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/organization_services")
 */
class OrganizationServiceController extends AbstractController
{
    /**
     * @Route("", methods={"POST"})
     * @IsGranted("ROLE_USER")
     * @param Request $request
     * @return Response
     */
    public function add(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['service'])) {
            return new Response('', Response::HTTP_BAD_REQUEST);
        }

        /** @var User $user */
        $user = $this->getUser();

        if (!$organization = $user->getOrganization()) {
            return new Response('', Response::HTTP_SERVICE_UNAVAILABLE);
        }

        if (!$service = $this->getDoctrine()->getRepository(Service::class)->find($data['service'])) {
            return new Response('', Response::HTTP_BAD_REQUEST);
        }

        $organizationService = new OrganizationService();

        $organizationService->setOrganization($organization)
                            ->setService($service);

        $em = $this->getDoctrine()->getManager();
        $em->persist($organizationService);
        $em->flush();

        return new Response();
    }

    /**
     * @Route("", methods={"GET"})
     * @IsGranted("ROLE_USER")
     * @return Response
     */
    public function fetchList(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if (!$organization = $user->getOrganization()) {
            return new Response('', Response::HTTP_SERVICE_UNAVAILABLE);
        }

        $response = [];

        foreach ($organization->getOrganizationServices() as $organizationService) {
            $response[] = [
                'id' => $organizationService->getId(),
                'name' => $organizationService->getService()->getName()
            ];
        }

        return $this->json($response);
    }

    /**
     * @Route("/{id}", methods={"DELETE"})
     * @param int $id
     * @return Response
     */
    public function delete(int $id): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if (!$organization = $user->getOrganization()) {
            return new Response('', Response::HTTP_SERVICE_UNAVAILABLE);
        }

        if (!$service = $this->getDoctrine()->getRepository(Service::class)->find($id)) {
            return new Response('', Response::HTTP_NOT_FOUND);
        }

        $organization->removeService($service);

        $em = $this->getDoctrine()->getManager();
        $em->persist($organization);
        $em->flush();

        return new Response();
    }
}
