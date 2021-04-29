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
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function fetchList(Request $request, PaginatorInterface $paginator): Response
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);

        $qb = $this->getDoctrine()
                   ->getRepository(Organization::class)
                   ->createQueryBuilder('organization')
                   ->addOrderBy('organization.name');

        $organizations = $paginator->paginate($qb->getQuery(), $page, $limit);

        $response = [];

        foreach ($organizations as $index => $organization) {
            $timezone = $organization->getTimezone();

            if ($timezone !== 0) {
                $timezone = ($timezone > 0 ? '+' : '-') . $timezone;
            }

            $response['data'][] = [
                'id' => $organization->getId(),
                'index' => ($index + 1) + ($page - 1) * $limit,
                'name' => $organization->getName(),
                'timezone' => $timezone
            ];
        }

        $response['count'] = $organizations->getTotalItemCount();

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

        return $this->json([
            'id' => $organization->getId(),
            'name' => $organization->getName(),
            'timezone' => $organization->getTimezone()
        ]);
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
        if (!is_int($data['timezone'])) throw new Exception('Часовой пояс должен быть числом в диапазоне от -12 до +12');

        if (null !== $id) {
            $organization = $this->getDoctrine()->getRepository(Organization::class)->find($id);
            if (!$organization) return new Response('', Response::HTTP_NOT_FOUND);
        } else {
            $organization = new Organization();
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
