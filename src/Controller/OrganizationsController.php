<?php

namespace App\Controller;

use App\Entity\Organization;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/organizations")
 * Class OrganizationsController
 * @package App\Controller
 */
class OrganizationsController extends AbstractController
{
    /**
     * @Route("/list")
     * @param Request $request
     * @return JsonResponse
     */
    public function fetchDictionary(Request $request): JsonResponse
    {
        $search = $request->query->get('filter');

        $organizations = $this->getDoctrine()->getRepository(Organization::class)->fetchDictionary($search);

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
     * @Route("", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     * @return JsonResponse
     */
    public function fetchList(): JsonResponse
    {
        $organizations = $this->getDoctrine()->getRepository(Organization::class)->findBy([], ['id' => 'asc']);

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

    /**
     * @Route("", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @return JsonResponse
     */
    public function add(Request $request): JsonResponse
    {
        $name = json_decode($request->getContent(), true)['name'];

        if (empty($name)) return $this->json(['message' => 'Необходимо указать название организации'], Response::HTTP_BAD_REQUEST);

        $organization = new Organization();
        $organization->setName($name);

        $em = $this->getDoctrine()->getManager();

        $em->persist($organization);;
        $em->flush();

        return $this->json([]);
    }

    /**
     * @Route("/{id}", methods={"GET"})
     * @param int $id
     * @return JsonResponse
     */
    public function fetch(int $id): JsonResponse
    {
        $organization = $this->getDoctrine()->getRepository(Organization::class)->find($id);

        if (null === $organization) return $this->json([], Response::HTTP_NOT_FOUND);

        return $this->json([
            'id' => $organization->getId(),
            'name' => $organization->getName()
        ]);
    }

    /**
     * @Route("/{id}", methods={"POST"})
     * @param int $id
     * @param Request $request
     * @return JsonResponse
     */
    public function update(int $id, Request $request): JsonResponse
    {
        $organization = $this->getDoctrine()->getRepository(Organization::class)->find($id);

        if (null === $organization) return $this->json([], Response::HTTP_NOT_FOUND);

        $name = json_decode($request->getContent(), true)['name'];

        $organization->setName($name);

        $em = $this->getDoctrine()->getManager();

        $em->persist($organization);;
        $em->flush();

        return $this->json([]);
    }

    /**
     * @Route("/{id}", methods={"DELETE"})
     * @param int $id
     * @return JsonResponse
     */
    public function delete(int $id): JsonResponse
    {
        $organization = $this->getDoctrine()->getRepository(Organization::class)->find($id);

        if (null === $organization) return $this->json([], Response::HTTP_NOT_FOUND);

        $em = $this->getDoctrine()->getManager();

        $em->remove($organization);
        $em->flush();

        return $this->json([]);
    }
}
