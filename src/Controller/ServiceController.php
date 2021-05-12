<?php

namespace App\Controller;

use App\Entity\Service;
use App\Entity\ServiceGroup;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

/**
 * @Route("/services", name="services_")
 * @IsGranted("ROLE_ADMIN")
 */
class ServiceController extends AbstractController
{
    /**
     * @Route("", methods={"GET"}, name="fetch_list")
     * @param Request $request
     * @return Response
     */
    public function fetchList(Request $request): Response
    {
        $servicesGroupID = $request->query->getInt('servicesGroupID', 0);

        $qb = $this->getDoctrine()
                   ->getRepository(Service::class)
                   ->createQueryBuilder('service');

        if ($servicesGroupID) {
            $qb->andWhere('service.serviceGroup = :serviceGroup')
               ->setParameter('serviceGroup', $servicesGroupID);
        }

        $services = $qb->getQuery()->getResult();

        $result = [];

        foreach ($services as $index => $service) {
            $result[] = [
                'id' => $service->getId(),
                'index' => $index + 1,
                'name' => $service->getName()
            ];
        }

        return $this->json($result);
    }

    /**
     * @Route("/{id}", methods={"GET"}, name="fetch")
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
     * @Route("", methods={"POST"}, name="add")
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
     * @param int|null $id
     * @return Response
     * @throws Exception
     */
    private function save(Request $request, ?int $id = null): Response
    {
        $servicesGroupID = $request->request->getInt('servicesGroupID', 0);
        $name = $request->get('name');

        if (empty($servicesGroupID)) return new Response('Неверный формат запроса', Response::HTTP_BAD_REQUEST);
        if (empty($name)) return new Response('Неверный формат запроса', Response::HTTP_BAD_REQUEST);

        $servicesGroup = $this->getDoctrine()->getRepository(ServiceGroup::class)->find($servicesGroupID);

        if (null !== $id) {
            $service = $this->getDoctrine()->getRepository(Service::class)->find($id);
            if (!$service) return new Response('', Response::HTTP_NOT_FOUND);
        } else {
            $service = new Service();
        }

        $service->setServiceGroup($servicesGroup)->setName($name);

        $em = $this->getDoctrine()->getManager();

        $em->persist($service);
        $em->flush();

        return new Response();
    }

    /**
     * @Route("/{id}", methods={"POST"}, name="update")
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
     * @Route("/{id}", methods={"DELETE"}, name="delete")
     * @IsGranted("ROLE_SUPER_ADMIN")
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
