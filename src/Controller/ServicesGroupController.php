<?php

namespace App\Controller;

use App\Entity\ServicesGroup;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/servicesGroups", name="services_groups_")
 */
class ServicesGroupController extends AbstractController
{
    /**
     * @Route("",methods={"POST"}, name="add")
     * @param Request $request
     * @return Response
     */
    public function add(Request $request): Response
    {
        return $this->save($request);
    }

    private function save(Request $request, ?int $id = null): Response
    {
        if (!$name = $request->request->get('name')) return new Response('', Response::HTTP_BAD_REQUEST);

        if ($id) {
            $servicesGroup = $this->getDoctrine()->getRepository(ServicesGroup::class)->find($id);

            if (!$servicesGroup) return new Response('', Response::HTTP_NOT_FOUND);
        } else {
            $servicesGroup = new ServicesGroup();
        }

        $servicesGroup->setName($name);

        $em = $this->getDoctrine()->getManager();
        $em->persist($servicesGroup);
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
        return $this->save($request, $id);
    }

    /**
     * @Route("/{id}", methods={"DELETE"}, name="delete")
     * @param int $id
     * @return Response
     */
    public function delete(int $id): Response
    {
        $servicesGroup = $this->getDoctrine()->getRepository(ServicesGroup::class)->find($id);

        if (!$servicesGroup) return new Response('', Response::HTTP_NOT_FOUND);

        $em = $this->getDoctrine()->getManager();

        $em->remove($servicesGroup);
        $em->flush();

        return new Response();
    }
}
