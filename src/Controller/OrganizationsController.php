<?php

namespace App\Controller;

use App\Entity\Organization;
use Exception;
use Knp\Component\Pager\PaginatorInterface;
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
         * @var int $index
         * @var Organization $organization
         */
        foreach ($organizations as $index => $organization) {
            $timezone = $organization->getTimezone();

            if ($timezone !== 0) {
                $timezone = ($timezone > 0 ? '+' : '-') . $timezone;
            }

            $org = [
                'id' => $organization->getId(),
                'index' => $index + 1,
                'name' => $organization->getName(),
                'timezone' => $timezone
            ];

            foreach ($organization->getBranches() as $ind => $branch) {
                $timezone = $branch->getTimezone();

                if ($timezone !== 0) {
                    $timezone = ($timezone > 0 ? '+' : '-') . $timezone;
                }

                $br = [
                    'id' => $branch->getId(),
                    'index' => ($ind + 1),
                    'name' => $branch->getName(),
                    'timezone' => $timezone
                ];

                $org['branches'][] = $br;
            }

            $response[] = $org;
        }

        return $this->json($response);
    }

    /**
     * @Route("/{id}", methods={"GET"}, name="fetch")
     * @param int $id
     * @return Response
     */
    public function fetch(int $id): Response
    {
        $organization = $this->getDoctrine()->getRepository(Organization::class)->find($id);

        if (!$organization) {
            return new Response('', Response::HTTP_NOT_FOUND);
        }

        $response = [
            'id' => $organization->getId(),
            'name' => $organization->getName(),
            'timezone' => $organization->getTimezone()
        ];

        if ($parent = $organization->getParent()) {
            $response['parent'] = [
                'value' => $parent->getId(),
                'label' => $parent->getName()
            ];
        }

        return $this->json($response);
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
        $data = json_decode($request->getContent(), true);

        if (empty($data['name'])) throw new Exception('Укажите название организации');
        if (empty($data['timezone'])) throw new Exception('Укажите часовой пояс');
        if (!is_int($data['timezone'])) throw new Exception('Разница в часах относительно МСК должно быть целым числом');

        if (null !== $id) {
            $organization = $this->getDoctrine()->getRepository(Organization::class)->find($id);
            if (!$organization) return new Response('', Response::HTTP_NOT_FOUND);
        } else {
            $organization = new Organization();
        }

        if (!empty($data['parent'])) {
            $parent = $this->getDoctrine()->getRepository(Organization::class)->find($data['parent']);

            if (!$parent) {
                return new Response('', Response::HTTP_BAD_REQUEST);
            }

            $organization->setParent($parent);
        }

        $organization->setName($data['name'])
                     ->setTimezone($data['timezone']);

        $em = $this->getDoctrine()->getManager();

        $em->persist($organization);
        $em->flush();

        $response = new Response();

        $response->setStatusCode($id ? Response::HTTP_OK : Response::HTTP_CREATED);

        return $response;
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
     * @param int $id
     * @return Response
     */
    public function delete(int $id): Response
    {
        $organization = $this->getDoctrine()->getRepository(Organization::class)->find($id);

        if (!$organization) {
            return new Response('', Response::HTTP_NOT_FOUND);
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($organization);
        $em->flush();

        return new Response();
    }
}
