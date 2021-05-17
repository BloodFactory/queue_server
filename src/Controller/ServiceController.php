<?php

namespace App\Controller;

use App\Entity\Service;
use App\Entity\ServicesGroup;
use Exception;
use Psr\Cache\InvalidArgumentException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\AdapterInterface;
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
    public const DICTIONARY_CACHE_KEY = 'dictionary.services';
    private AdapterInterface $cache;

    public function __construct(AdapterInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @Route("", methods={"GET"}, name="fetch_list")
     * @param Request $request
     * @return Response
     */
    public function fetchList(Request $request): Response
    {
        $filter = $request->query->get('filter', '');

        $qb = $this->getDoctrine()
                   ->getRepository(ServicesGroup::class)
                   ->createQueryBuilder('services_group')
                   ->addSelect('services')
                   ->addOrderBy('services_group.name')
                   ->addOrderBy('services.name');

        if ($filter) {
            $qb->leftJoin('services_group.services', 'services', 'WITH', 'services.name LIKE :filter')
               ->orWhere('services_group.name like :filter')
               ->orWhere('services.name LIKE :filter')
               ->setParameter('filter', "%${filter}%");
        } else {
            $qb->leftJoin('services_group.services', 'services',);
        }

        $servicesGroups = $qb->getQuery()->getResult();

        $result = [];

        foreach ($servicesGroups as $servicesGroupIndex => $servicesGroup) {
            $item = [
                'id' => $servicesGroup->getId(),
                'index' => $servicesGroupIndex + 1,
                'name' => $servicesGroup->getName()
            ];

            $services = [];

            foreach ($servicesGroup->getServices() as $serviceIndex => $service) {
                $services[] = [
                    'id' => $service->getId(),
                    'index' => $serviceIndex + 1,
                    'name' => $service->getName()
                ];
            }

            $item['services'] = $services;

            $result[] = $item;
        }

        return $this->json($result);
    }

    /**
     * @Route("", methods={"POST"}, name="add")
     * @param Request $request
     * @return Response
     * @throws InvalidArgumentException
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
     * @throws InvalidArgumentException
     */
    private function save(Request $request, ?int $id = null): Response
    {
        $servicesGroupID = $request->request->getInt('servicesGroupID', 0);
        $name = $request->get('name');

        if (empty($servicesGroupID)) return new Response('Неверный формат запроса', Response::HTTP_BAD_REQUEST);
        if (empty($name)) return new Response('Неверный формат запроса', Response::HTTP_BAD_REQUEST);

        $servicesGroup = $this->getDoctrine()->getRepository(ServicesGroup::class)->find($servicesGroupID);

        if (null !== $id) {
            $service = $this->getDoctrine()->getRepository(Service::class)->find($id);
            if (!$service) return new Response('', Response::HTTP_NOT_FOUND);
        } else {
            $service = new Service();
        }

        $service->setServiceGroup($servicesGroup)->setName($name);

        $em = $this->getDoctrine()->getManager();

        $em->persist($service);

        try {
            $em->flush();
        } catch (Throwable $e) {
            return new Response('Неудалсоь выполнить запрос', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

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
     * @param int $id
     * @param Request $request
     * @return Response
     * @throws InvalidArgumentException
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
     * @throws InvalidArgumentException
     */
    public function dictionary(AdapterInterface $cache): Response
    {
        $item = $cache->getItem(self::DICTIONARY_CACHE_KEY);

        $result = [];

        if (!$item->isHit()) {
            $servicesGroups = $this->getDoctrine()->getRepository(ServicesGroup::class)->findAll();

            foreach ($servicesGroups as $servicesGroupIndex => $servicesGroup) {
                $result[$servicesGroupIndex] = [
                    'value' => $servicesGroup->getId(),
                    'label' => $servicesGroup->getName()
                ];

                foreach ($servicesGroup->getServices() as $serviceIndex => $service) {
                    $result[$servicesGroupIndex]['services'][$serviceIndex] = [
                        'value' => $service->getId(),
                        'label' => $service->getName()
                    ];
                }
            }

            $item->set($result);
            $cache->save($item);
        } else {
            $result = $item->get();
        }

        return $this->json($result);
    }
}
