<?php

namespace App\Controller;

use App\Entity\Organization;
use App\Service\Dictionary\Service as ServiceDictionary;
use Exception;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

/**
 * @Route("/organizations", name="organizations_")
 */
class OrganizationsController extends AbstractController
{
    private ServiceDictionary $dictionary;

    public function __construct(ServiceDictionary $dictionary)
    {
        $this->dictionary = $dictionary;
    }

    /**
     * @Route("", methods={"GET"}, name="fetch_list")
     * @return Response
     */
    public function fetchList(): Response
    {
        $organizations = $this->getDoctrine()
                              ->getRepository(Organization::class)
                              ->createQueryBuilder('organization')
                              ->addSelect('branches')
                              ->leftJoin('organization.branches', 'branches')
                              ->andWhere('organization.parent IS NULL')
                              ->addOrderBy('organization.name')
                              ->getQuery()
                              ->getResult();

        $response = [];

        /**
         * @var int $organizationIndex
         * @var Organization $organization
         */
        foreach ($organizations as $organizationIndex => $organization) {
            $org = [
                'id' => $organization->getId(),
                'index' => $organizationIndex + 1,
                'name' => $organization->getName(),
                'timezone' => $organization->getTimezone()
            ];

            $branches = [];

            foreach ($organization->getBranches() as $branchIndex => $branch) {
                $branches[] = [
                    'id' => $branch->getId(),
                    'index' => $branchIndex + 1,
                    'name' => $branch->getName(),
                    'timezone' => $branch->getTimezone()
                ];
            }

            $org['branches'] = $branches;

            $response[] = $org;
        }

        return $this->json($response);
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
        $name = $request->request->get('name');
        $timezone = $request->request->getInt('timezone');
        $parentID = $request->request->getInt('parent', 0);

        if (empty($name)) throw new Exception('Укажите название организации');
        if (empty($timezone)) throw new Exception('Укажите часовой пояс');
        if (!is_int($timezone)) throw new Exception('Разница в часах относительно МСК должно быть целым числом');

        if (null !== $id) {
            $organization = $this->getDoctrine()->getRepository(Organization::class)->find($id);
            if (!$organization) return new Response('', Response::HTTP_NOT_FOUND);
        } else {
            $organization = new Organization();
        }

        if (!empty($parentID)) {
            $parent = $this->getDoctrine()->getRepository(Organization::class)->find($parentID);

            if (!$parent) {
                return new Response('', Response::HTTP_BAD_REQUEST);
            }

            $organization->setParent($parent);
        }

        $organization->setName($name)
                     ->setTimezone($timezone);

        $em = $this->getDoctrine()->getManager();

        $em->persist($organization);
        $em->flush();

        $response = new Response();

        $response->setStatusCode($id ? Response::HTTP_OK : Response::HTTP_CREATED);

        $this->dictionary->clear();

        return $response;
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
     * @param int $id
     * @return Response
     * @throws InvalidArgumentException
     */
    public function delete(int $id): Response
    {
        $organization = $this->getDoctrine()->getRepository(Organization::class)->find($id);

        if (!$organization) {
            return new Response('', Response::HTTP_NOT_FOUND);
        }

        if ($organization->getBranches()->count() > 0) {
            return new Response('Нельзя удалить организацию, так как имеются связанные с ней филиалы', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($organization);
        $em->flush();

        $this->dictionary->clear();

        return new Response();
    }


}
