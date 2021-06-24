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
 */
class ServiceGroupController extends AbstractController
{
    /**
     * @Route("", name="add", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @return Response
     */
    public function add(Request $request): Response
    {
        if (!$name = $request->request->get('name')) {
            return new Response('Неверный формат запроса', Response::HTTP_BAD_REQUEST);
        }

        $serviceGroup = new ServiceGroup();
        $serviceGroup->setName($name);

        if ($parentId = $request->request->getInt('parent')) {
            if (!$parent = $this->getDoctrine()->getRepository(ServiceGroup::class)->find($parentId)) {
                return new Response('Неверный формат запроса', Response::HTTP_BAD_REQUEST);
            }

            $serviceGroup->setParent($parent);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($serviceGroup);
        $em->flush();

        return new Response();
    }

    /**
     * @Route("/{id}", name="update", methods={"POST"})
     * @param int $id
     * @param Request $request
     * @return Response
     */
    public function update(int $id, Request $request): Response
    {
        if (!$name = $request->request->get('name')) {
            return new Response('Неверный формат запроса', Response::HTTP_BAD_REQUEST);
        }

        if (!$serviceGroup = $this->getDoctrine()->getRepository(ServiceGroup::class)->find($id)) {
            return new Response('', Response::HTTP_NOT_FOUND);
        }

        $serviceGroup->setName($name);
        $em = $this->getDoctrine()->getManager();
        $em->persist($serviceGroup);
        $em->flush();
        return new Response();
    }

    /**
     * @Route("/{id}", methods={"DELETE"}, name="delete")
     * @IsGranted("ROLE_SUPER_ADMIN")
     * @param int $id
     * @return Response
     */
    public function delete(int $id): Response
    {
        $serviceGroup = $this->getDoctrine()->getRepository(ServiceGroup::class)->find($id);

        if (!$serviceGroup) {
            return new Response('', Response::HTTP_NOT_FOUND);
        }

        if ($serviceGroup->getChildren()->count() > 0) {
            return new Response('Запись нельзя удалить, так как имеются зависимые от ней записи', Response::HTTP_BAD_REQUEST);
        }

        if ($serviceGroup->getServices()->count() > 0) {
            return new Response('Запись нельзя удалить, так как имеются зависимые от ней записи', Response::HTTP_BAD_REQUEST);
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($serviceGroup);
        $em->flush();

//        $this->clearCache();

        return new Response();
    }
}
