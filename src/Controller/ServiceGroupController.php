<?php

namespace App\Controller;

use App\Entity\ServiceGroup;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/servicesGroups", name="services_groups_")
 * @IsGranted("ROLE_ADMIN")
 */
class ServiceGroupController extends AbstractController
{
    /**
     * @Route("", methods={"GET"}, name="list")
     * @return Response
     */
    public function fetchList(): Response
    {
        $servicesGroups = $this->getDoctrine()->getRepository(ServiceGroup::class)->findAll();

        $result = [];

        foreach ($servicesGroups as $index => $servicesGroup) {
            $item = [
                'id' => $servicesGroup->getId(),
                'index' => $index + 1,
                'name' => $servicesGroup->getName(),
            ];

            $result[] = $item;
        }

        return $this->json($result);
    }

    /**
     * @Route("", methods={"POST"}, name="add")
     * @param Request $request
     * @return Response
     */
    public function add(Request $request): Response
    {
        return $this->save($request);
    }

    /**
     * @param Request $request
     * @param int|null $id
     * @return Response
     */
    private function save(Request $request, ?int $id = null): Response
    {
        if (!$name = $request->request->get('name')) {
            return new Response('Неверный формат запроса', Response::HTTP_BAD_REQUEST);
        }

        if ($id) {
            if (!$serviceGroup = $this->getDoctrine()->getRepository(ServiceGroup::class)->find($id)) {
                return new Response('', Response::HTTP_NOT_FOUND);
            }
        } else {
            $serviceGroup = new ServiceGroup();
        }

        $serviceGroup->setName($name);

        $em = $this->getDoctrine()->getManager();

        $em->persist($serviceGroup);
        $em->flush();

        return new Response('');
    }

    /**
     * @Route("/{id}", methods={"POST"}, name="update")
     * @param int $id
     * @param Request $request
     * @return Response
     */
    public function update(int $id, Request $request): Response
    {
        return $this->save($request, $id);
    }

    /**
     * @Route("/{id}", methods={"DELETE"}, name="delete")
     * @IsGranted("ROLE_SUPER_ADMIN")
     * @param int $id
     * @return Response
     */
    public function delete(int $id): Response
    {
        if (!$serviceGroup = $this->getDoctrine()->getRepository(ServiceGroup::class)->find($id)) {
            return new Response('Запись не найдена', Response::HTTP_FOUND);
        }

        $em = $this->getDoctrine()->getManager();

        $em->remove($serviceGroup);
        $em->flush();

        return new Response();
    }
}
