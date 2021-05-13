<?php

namespace App\Controller;

use App\Entity\ServicesGroup;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

/**
 * @Route("/servicesGroups", name="services_groups_")
 */
class ServicesGroupController extends AbstractController
{
    private AdapterInterface $cache;

    public function __construct(AdapterInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @Route("",methods={"POST"}, name="add")
     * @param Request $request
     * @return Response
     * @throws InvalidArgumentException
     */
    public function add(Request $request): Response
    {
        return $this->save($request);
    }

    /**
     * @param Request $request
     * @param int|null $id
     * @return Response
     * @throws InvalidArgumentException
     */
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

        try {
            $em->flush();
        } catch (Throwable $e) {
            return new Response('Неудалось выполнить запрос', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $this->clearCache();

        return new Response();
    }

    /**
     * @throws InvalidArgumentException
     */
    private function clearCache(): void
    {
        $this->cache->deleteItem(ServiceController::DICTIONARY_CACHE_KEY);
    }

    /**
     * @Route("/{id}", methods={"POST"}, name="update")
     * @param int $id
     * @param Request $request
     * @return Response
     * @throws InvalidArgumentException
     */
    public function update(int $id, Request $request): Response
    {
        return $this->save($request, $id);
    }

    /**
     * @Route("/{id}", methods={"DELETE"}, name="delete")
     * @param int $id
     * @return Response
     * @throws InvalidArgumentException
     */
    public function delete(int $id): Response
    {
        $servicesGroup = $this->getDoctrine()->getRepository(ServicesGroup::class)->find($id);

        if (!$servicesGroup) return new Response('', Response::HTTP_NOT_FOUND);

        $em = $this->getDoctrine()->getManager();

        $em->remove($servicesGroup);
        $em->flush();

        $this->clearCache();

        return new Response();
    }
}
