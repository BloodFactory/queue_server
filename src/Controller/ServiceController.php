<?php

namespace App\Controller;

use App\Entity\Service;
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
        $tmp = [];

        $services = $this->getDoctrine()
                         ->getRepository(Service::class)
                         ->createQueryBuilder('s')
                         ->addSelect('p')
                         ->leftJoin('s.parent', 'p')
                         ->getQuery()
                         ->getArrayResult();

        foreach ($services as $service) {
            $service['parent'] = $service['parent'] ? $service['parent']['id'] : null;
            $tmp[$service['id']] = $service;
        }

        $services = $tmp;
        $children = [];

        foreach ($services as $serviceIndex => $service) {
            if ($service['parent']) {
                $parent = $service['parent'];
                unset($service['parent']);
                $children[$parent][$service['id']] = $service;
                unset($services[$serviceIndex]);
            }
        }

        $test = function ($services) use ($children, &$test) {
            $result = [];

            $i = 1;
            foreach ($services as $serviceIndex => $service) {
                $service['index'] = $i;

                if (isset($children[$service['id']])) {
                    $service['children'] = $test($children[$service['id']]);
                }

                $result[] = $service;

                $i++;
            }

            return $result;
        };

        $services = $test($services);

        return $this->json($services);
    }

    /**
     * @Route("", methods={"POST"}, name="add")
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @return Response
     * @throws InvalidArgumentException
     */
    public function add(Request $request): Response
    {
        if (!$name = $request->request->get('name')) {
            return new Response('Неверный формат запроса', Response::HTTP_BAD_REQUEST);
        }

        $service = new Service();
        $service->setName($name);

        if ($parentId = $request->request->getInt('parent')) {
            if (!$parent = $this->getDoctrine()->getRepository(Service::class)->find($parentId)) {
                return new Response('Неверный формат запроса', Response::HTTP_BAD_REQUEST);
            }

            $service->setParent($parent);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($service);
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
