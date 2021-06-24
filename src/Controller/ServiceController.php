<?php

namespace App\Controller;

use App\Entity\Service;
use App\Entity\ServiceGroup;
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
     * @return Response
     */
    public function fetchList(): Response
    {
        $data = $this->getDoctrine()
                     ->getRepository(ServiceGroup::class)
                     ->createQueryBuilder('sg')
                     ->addSelect('s')
                     ->addSelect('p')
                     ->leftJoin('sg.services', 's')
                     ->leftJoin('sg.parent', 'p')
                     ->addOrderBy('sg.name')
                     ->getQuery()
                     ->getArrayResult();

        $children = [];
        foreach ($data as $index => &$item) {
            $item['children'] = [];
            if (null !== $item['parent']) {
                $item['parent'] = $item['parent']['id'];
            } else {
                unset($data[$index]['parent']);
            }

            $children[$item['id']] = &$item;
        }

        foreach ($data as $index => &$item) {
            if (isset($item['parent'])) {
                $children[$item['parent']]['children'][] = $item;
                array_splice($data, $index, 1);
            }
        }

        $services = $this->getDoctrine()
                         ->getRepository(Service::class)
                         ->createQueryBuilder('s')
                         ->andWhere('s.serviceGroup IS NULL')
                         ->getQuery()
                         ->getArrayResult();

        return $this->json(['services' => $services, 'children' => $data]);
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

        if ($serviceGroupId = $request->request->getInt('serviceGroupId')) {

            if (!$serviceGroup = $this->getDoctrine()->getRepository(ServiceGroup::class)->find($serviceGroupId)) {
                return new Response('Неверный формат запроса', Response::HTTP_BAD_REQUEST);
            }

            $service->setServiceGroup($serviceGroup);
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
     * @throws InvalidArgumentException
     */
    public function update(int $id, Request $request): Response
    {
        if (!$name = $request->request->get('name')) {
            return new Response('Неверный формат запроса', Response::HTTP_BAD_REQUEST);
        }

        if (!$service = $this->getDoctrine()->getRepository(Service::class)->find($id)) {
            return new Response('', Response::HTTP_NOT_FOUND);
        }
        $service->setName($name);
        $em = $this->getDoctrine()->getManager();
        $em->persist($service);
        $em->flush();
        $this->clearCache();
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
}
