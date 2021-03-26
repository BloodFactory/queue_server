<?php

namespace App\Controller;

use App\Entity\Organization;
use App\Entity\User;
use App\Entity\UserData;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Throwable;

/**
 * @Route("/users")
 * Class UsersController
 * @package App\Controller
 */
class UsersController extends AbstractController
{
    private UserPasswordEncoderInterface $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @Route("", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     * @return JsonResponse
     */
    public function fetchList(): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $users = $this->getDoctrine()->getRepository(User::class)->fetchList($user);

        $response = [];

        foreach ($users as $index => $user) {
            $item = [
                'index' => $index + 1,
                'isActive' => $user->getIsActive(),
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'organization' => $user->getOrganization() ? $user->getOrganization()->getName() : '',
                'fio' => ($userData = $user->getUserData()) ? implode(' ', [
                    $userData->getLastName() ?? '',
                    $userData->getFirstName() ?? '',
                    $userData->getMiddleName() ?? ''
                ]) : ''
            ];

            $response[] = $item;
        }

        return $this->json($response);
    }

    /**
     * @Route("", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return JsonResponse
     */
    public function add(Request $request, UserPasswordEncoderInterface $passwordEncoder): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $this->saveUser($data);
        } catch (Throwable $e) {
            return $this->json([], Response::HTTP_BAD_REQUEST);
        }
        return $this->json([]);
    }

    /**
     * @param array $data
     * @param int|null $id
     * @throws Exception
     */
    private function saveUser(array $data, ?int $id = null): void
    {
        $user = $id ? $this->getDoctrine()->getRepository(User::class)->find($id) : new User();
        if (!$userData = $user->getUserData()) {
            $userData = new UserData();
            $user->setUserData($userData);
        }

        $userData = $user->getUserData() ?? new UserData();
        if (empty($data['username'])) throw new Exception();
        if (empty($data['organization'])) throw new Exception();
        if (empty($data['userData']['lastName'])) throw new Exception();
        if (empty($data['userData']['firstName'])) throw new Exception();

        if (null === $id) {
            if (empty($data['password'])) throw new Exception();
            if (empty($data['confirmPassword'])) throw new Exception();
            if ($data['password'] !== $data['confirmPassword']) throw new Exception();
        } else {
            if (!empty($data['password'])) {
                if (empty($data['confirmPassword']) || $data['password'] !== $data['confirmPassword']) throw new Exception();
            }
        }

        $organization = $this->getDoctrine()->getRepository(Organization::class)->find($data['organization']);

        if (null === $organization) throw new Exception();

        $user->setUsername($data['username'])
             ->setRoles(['ROLE_CLIENT'])
             ->setOrganization($organization);

        if (!empty($data['password'])) $user->setPassword($this->passwordEncoder->encodePassword($user, $data['password']));

        $userData->setLastName($data['userData']['lastName'])
                 ->setFirstName($data['userData']['firstName']);

        if (isset($data['userData']['middleName'])) $userData->setMiddleName($data['userData']['middleName']);

        $em = $this->getDoctrine()->getManager();
        $em->persist($userData);
        $em->persist($user);
        $em->flush();
    }

    /**
     * @Route("/{id}", methods={"GET"})
     * @param int $id
     * @return JsonResponse
     */
    public function fetch(int $id): JsonResponse
    {
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);

        if (null === $user) return $this->json([], Response::HTTP_NOT_FOUND);

        $response = [
            'id' => $user->getId(),
            'isActive' => $user->getIsActive(),
            'username' => $user->getUsername(),
            'organization' => $user->getOrganization() ? [
                'value' => $user->getOrganization()->getId(),
                'label' => $user->getOrganization()->getName()
            ] : ''
        ];

        $userData = $user->getUserData();

        $response['userData'] = [
            'lastName' => $userData && $userData->getLastName() ? $userData->getLastName() : '',
            'firstName' => $userData && $userData->getFirstName() ? $userData->getFirstName() : '',
            'middleName' => $userData && $userData->getMiddleName() ? $userData->getMiddleName() : '',
        ];

        return $this->json($response);
    }

    /**
     * @Route("/{id}", methods={"POST"})
     * @param int $id
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return JsonResponse
     */
    public function update(int $id, Request $request, UserPasswordEncoderInterface $passwordEncoder): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            $this->saveUser($data, $id);
        } catch (Throwable $e) {
            return $this->json([], Response::HTTP_BAD_REQUEST);
        }

        return $this->json([]);
    }

    /**
     * @Route("/{id}", methods={"DELETE"})
     * @param int $id
     * @return JsonResponse
     */
    public function delete(int $id): JsonResponse
    {
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);

        if (null === $user) return $this->json([], Response::HTTP_NOT_FOUND);

        $em = $this->getDoctrine()->getManager();

        $em->remove($user);
        $em->flush();

        return $this->json([]);
    }

    /**
     * @Route("/{id}/toggle", methods={"POST"})
     * @param int $id
     * @return JsonResponse
     */
    public function toggle(int $id): JsonResponse
    {
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);

        if (!$user) return $this->json([], Response::HTTP_NOT_FOUND);
        $user->setIsActive(!$user->getIsActive());

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        return $this->json([]);
    }
}
