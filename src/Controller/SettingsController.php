<?php

namespace App\Controller;

use App\Entity\Service;
use App\Entity\User;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/settings")
 * Class SettingsController
 * @package App\Controller
 */
class SettingsController extends AbstractController
{
    /**
     * @Route("", methods={"GET"})
     * @IsGranted("ROLE_CLIENT")
     * @return JsonResponse
     */
    public function fetchList(): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        $organization = $user->getOrganization();

        $settings = $this->getDoctrine()->getRepository(Service::class)->findBy(['organization' => $organization->getId()]);

        $response = [];

        foreach ($settings as $index => $set) {
            $response[] = [
                'id' => $set->getId(),
                'index' => $index,
                'isActive' => $set->getIsActive(),
                'name' => $set->getName()
            ];
        }

        return $this->json($response);
    }

    /**
     * @Route("/{id}", methods={"GET"})
     * @IsGranted("ROLE_CLIENT")
     * @param int $id
     * @return JsonResponse
     */
    public function fetch(int $id): JsonResponse
    {
        $service = $this->getDoctrine()->getRepository(Service::class)->find($id);

        if (!$service) return $this->json([], Response::HTTP_NOT_FOUND);

        return $this->json([
            'id' => $service->getId(),
            'isActive' => $service->getIsActive(),
            'name' => $service->getName(),
            'days' => $service->getDays(),
            'receptionTime' => [
                'from' => $service->getReceptionTimeFrom()->format('H:i'),
                'till' => $service->getReceptionTimeTill()->format('H:i')
            ],
            'restTime' => [
                'from' => $service->getRestTimeFrom()->format('H:i') ?? '',
                'till' => $service->getRestTimeTill()->format('H:i') ?? ''
            ],
            'duration' => $service->getDuration(),
            'rest' => $service->getRest(),
            'persons' => $service->getPersons()
        ]);
    }

    /**
     * @Route("", methods={"POST"})
     * @IsGranted("ROLE_CLIENT")
     * @param Request $request
     * @return Response
     */
    public function add(Request $request): Response
    {
        try {
            $data = json_decode($request->getContent(), true);
            $this->save($data);
        } catch (Exception $e) {
            return $this->json([
                'message' => 'Заполните все поля корректно'
            ], Response::HTTP_BAD_REQUEST);
        }

        return new Response();
    }

    /**
     * @param array $data
     * @param int|null $id
     * @throws Exception
     */
    private function save(array $data, ?int $id = null)
    {
        $this->checkData($data);

        /** @var User $user */
        $user = $this->getUser();

        $organization = $user->getOrganization();

        $service = $id ? $this->getDoctrine()->getRepository(Service::class)->find($id) : new Service();

        $service->setOrganization($organization)
                ->setName($data['service'])
                ->setDays($data['days'])
                ->setReceptionTimeFrom(new \DateTime($data['receptionTime']['from']))
                ->setReceptionTimeTill(new \DateTime($data['receptionTime']['till']))
                ->setDuration($data['duration'])
                ->setPersons($data['persons']);

        if (!empty($data['restTime']['from']) && !empty($data['restTime']['till'])) {
            $service->setRestTimeFrom(new \DateTime($data['restTime']['from']))
                    ->setRestTimeTill(new \DateTime($data['restTime']['till']));
        }

        if (!empty($data['break'])) $service->setRest($data['break']);

        $em = $this->getDoctrine()->getManager();

        $em->persist($service);
        $em->flush();
    }

    /**
     * @param array $data
     * @throws Exception
     */
    private function checkData(array $data): void
    {
        if (empty($data['service'])) throw new Exception();
        if (empty($data['days'])) throw new Exception();
        if (empty($data['receptionTime']['from'])) throw new Exception();
        if (empty($data['receptionTime']['till'])) throw new Exception();
        if (empty($data['duration'])) throw new Exception();
        if (empty($data['persons'])) throw new Exception();
        if (empty($data['restTime']['from']) ^ empty($data['restTime']['till'])) throw new Exception();
    }

    /**
     * @Route("/{id}", methods={"POST"})
     * @IsGranted("ROLE_CLIENT")
     * @param int $id
     * @param Request $request
     * @return JsonResponse
     */
    public function update(int $id, Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $this->save($data, $id);
        } catch (Exception $e) {
            return $this->json([
                'message' => 'Заполните все поля корректно'
            ], Response::HTTP_BAD_REQUEST);
        }

        return $this->json([]);
    }

    /**
     * @Route("/{id}/toggle", methods={"POST"})
     * @param int $id
     * @return JsonResponse
     */
    public function toggle(int $id): JsonResponse
    {
        $service = $this->getDoctrine()->getRepository(Service::class)->find($id);

        if (!$service) return $this->json([], Response::HTTP_NOT_FOUND);
        $service->setIsActive(!$service->getIsActive());

        $em = $this->getDoctrine()->getManager();
        $em->persist($service);
        $em->flush();

        return $this->json([]);
    }

    /**
     * @Route("/{id}", methods={"DELETE"})
     * @param int $id
     * @return JsonResponse
     */
    public function delete(int $id):JsonResponse
    {
        $service = $this->getDoctrine()->getRepository(Service::class)->find($id);

        if (null === $service) return $this->json([], Response::HTTP_NOT_FOUND);

        $em = $this->getDoctrine()->getManager();

        $em->remove($service);
        $em->flush();

        return $this->json([]);
    }
}
