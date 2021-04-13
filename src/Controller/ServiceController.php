<?php

namespace App\Controller;

use App\Entity\OrganizationService;
use App\Entity\Service;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

/**
 * @Route("/services")
 */
class ServiceController extends AbstractController
{
    /**
     * @Route("", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     * @return Response
     */
    public function fetchList(): Response
    {
        $services = $this->getDoctrine()->getRepository(Service::class)->findAll();

        $response = [];

        foreach ($services as $index => $service) {
            $response[] = [
                'id' => $service->getId(),
                'index' => $index + 1,
                'name' => $service->getName()
            ];
        }

        return $this->json($response);
    }

    /**
     * @Route("/{id}", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     * @param int $id
     * @return Response
     */
    public function fetch(int $id): Response
    {
        $service = $this->getDoctrine()->getRepository(Service::class)->find($id);

        if (!$service) {
            return new Response('', Response::HTTP_NOT_FOUND);
        }

        return $this->json([
            'id' => $service->getId(),
            'name' => $service->getName()
        ]);
    }

    /**
     * @Route("", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @return Response
     */
    public function add(Request $request): Response
    {
        try {
            return $this->save($request);
        } catch (Throwable $e) {
            return new Response($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param Request $request
     * @IsGranted("ROLE_ADMIN")
     * @param int|null $id
     * @return Response
     * @throws Exception
     */
    private function save(Request $request, ?int $id = null): Response
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['name'])) throw new Exception('Укажите название услуги');

        if (null !== $id) {
            $service = $this->getDoctrine()->getRepository(Service::class)->find($id);
            if (!$service) return new Response('', Response::HTTP_NOT_FOUND);
        } else {
            $service = new Service();
        }

        $service->setName($data['name']);

        $em = $this->getDoctrine()->getManager();

        $em->persist($service);
        $em->flush();

        $response = new Response();

        $response->setStatusCode($id ? Response::HTTP_OK : Response::HTTP_CREATED);

        return $response;
    }

    /**
     * @Route("/{id}", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     * @param int $id
     * @param Request $request
     * @return Response
     */
    public function update(int $id, Request $request): Response
    {
        try {
            return $this->save($request, $id);
        } catch (Throwable $e) {
            return new Response($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Route("/{id}", methods={"DELETE"})
     * @IsGranted("ROLE_ADMIN")
     * @param int $id
     * @return Response
     */
    public function delete(int $id): Response
    {
        $service = $this->getDoctrine()->getRepository(Service::class)->find($id);

        if (!$service) {
            return new Response('', Response::HTTP_NOT_FOUND);
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($service);
        $em->flush();

        return new Response();
    }
}
