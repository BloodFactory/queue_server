<?php

namespace App\Controller;

use App\Entity\Service;
use App\Entity\ServicesGroup;
use Psr\Cache\InvalidArgumentException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/services", name="services_")
 */
class ServiceController extends AbstractController
{
    public const DICTIONARY_CACHE_KEY = 'dictionary.services';
    private AdapterInterface $cache;

    public function __construct(AdapterInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @Route("", methods={"GET"}, name="fetch_list")
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @return Response
     */
    public function fetchList(Request $request): Response
    {
        $result = [];

        return $this->json($result);
    }

    /**
     * @Route("", methods={"POST"}, name="add")
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @return Response
     */
    public function add(Request $request): Response
    {
        return new Response();
    }

    /**
     * @Route("/{id}", methods={"POST"}, name="update")
     * @IsGranted("ROLE_ADMIN")
     * @param int $id
     * @param Request $request
     * @return Response
     */
    public function update(int $id, Request $request): Response
    {
        return new Response();
    }

    /**
     * @Route("/{id}", methods={"DELETE"}, name="delete")
     * @IsGranted("ROLE_SUPER_ADMIN")
     * @param int $id
     * @return Response
     * @throws InvalidArgumentException
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

        $this->clearCache();

        return new Response();
    }

    /**
     * @throws InvalidArgumentException
     */
    private function clearCache(): void
    {
        $this->cache->deleteItem(self::DICTIONARY_CACHE_KEY);
    }

    /**
     * @Route("/dictionary", methods={"GET"}, name="dictionary")
     * @param AdapterInterface $cache
     * @return Response
     */
    public function dictionary(AdapterInterface $cache): Response
    {
        $result = [];

        return $this->json($result);
    }
}
