<?php

namespace App\Controller;

use App\Entity\Organization;
use App\Entity\User;
use App\Entity\UserData;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Throwable;

/**
 * @Route("/users")
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
     * @return Response
     */
    public function fetchList(): Response
    {
        $users = $this->getDoctrine()->getRepository(User::class)->createQueryBuilder('u')
                      ->addSelect('ud')
                      ->addSelect('o')
                      ->leftJoin('u.userData', 'ud')
                      ->leftJoin('u.organization', 'o')
                      ->andWhere('u.id != :id')
                      ->setParameter('id', $this->getUser()->getId())
                      ->getQuery()
                      ->getResult();

        $response = [];

        foreach ($users as $index => $user) {
            $response[] = $this->transformUserToArray($user);
        }

        return $this->json($response);
    }

    private function transformUserToArray(User $user): array
    {
        $userData = $user->getUserData();
        return [
            'id' => $user->getId(),
            'isActive' => $user->getIsActive(),
            'username' => $user->getUsername(),
            'organization' => $user->getOrganization() ? $user->getOrganization()->getName() : '',
            'userData' => $userData ? [
                'lastName' => $userData->getLastName() ?? '',
                'firstName' => $userData->getFirstName() ?? '',
                'middleName' => $userData->getMiddleName() ?? '',
            ] : []
        ];
    }

    /**
     * @Route("/{id}")
     * @IsGranted("ROLE_ADMIN")
     * @param int $id
     * @param Request $request
     * @return Response
     */
    public function fetch(int $id, Request $request): Response
    {
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);

        if (!$user) {
            return new Response('', Response::HTTP_NOT_FOUND);
        }

        return $this->json($this->transformUserToArray($user));
    }

    /**
     * @Route("", methods={"POST"})
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

        if (empty($data['username'])) return new Response('Введите имя пользователя', Response::HTTP_BAD_REQUEST);
        if (empty($data['organization'])) return new Response('Введите организацию', Response::HTTP_BAD_REQUEST);
        if (empty($data['userData']['lastName'])) return new Response('Введите фамилию', Response::HTTP_BAD_REQUEST);
        if (empty($data['userData']['firstName'])) return new Response('Введите имя', Response::HTTP_BAD_REQUEST);

        if (null === $id) {
            if (empty($data['password'])) return new Response('Введите пароль', Response::HTTP_BAD_REQUEST);
            if (empty($data['confirmPassword'])) return new Response('Введите подтверждение пароля', Response::HTTP_BAD_REQUEST);
            if ($data['password'] !== $data['confirmPassword']) return new Response('Пароль не совпадает с подтверждением пароля', Response::HTTP_BAD_REQUEST);
        } else {
            if (!empty($data['password'])) {
                if (empty($data['confirmPassword']) || $data['password'] !== $data['confirmPassword']) return new Response('Пароль не совпадает с подтверждением пароля', Response::HTTP_BAD_REQUEST);
            }
        }

        $organization = $this->getDoctrine()->getRepository(Organization::class)->find($data['organization']);

        if (null === $organization) return new Response('Указаной организации не существует', Response::HTTP_BAD_REQUEST);

        $user = $id ? $this->getDoctrine()->getRepository(User::class)->find($id) : new User();

        if (!$userData = $user->getUserData()) {
            $userData = new UserData();
            $user->setUserData($userData);
        }

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

        return new Response();
    }

    /**
     * @Route("/{id}/toggle", methods={"POST"})
     * @param int $id
     * @return Response
     */
    public function toggle(int $id): Response
    {
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);

        if (!$user) return new Response('', Response::HTTP_NOT_FOUND);
        
        $user->setIsActive(!$user->getIsActive());

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        return new Response();
    }
}
