<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserData;
use Exception;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Throwable;

/**
 * @Route("/users", name="users")
 */
class UsersController extends AbstractController
{
    private UserPasswordEncoderInterface $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @Route("", methods={"GET"}, name="fetch_list")
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function fetchList(Request $request, PaginatorInterface $paginator): Response
    {


        $qb = $this->getDoctrine()
                   ->getRepository(User::class)
                   ->createQueryBuilder('u')
                   ->addSelect('ud')
                   ->addSelect('o')
                   ->leftJoin('u.userData', 'ud')
                   ->leftJoin('u.organization', 'o')
                   ->andWhere('u.id != :id')
                   ->setParameter('id', $this->getUser()->getId());

        $users = $qb->getQuery()->getResult();

        $response = [];

        foreach ($users as $index => $user) {
            $item = $this->transformUserToArray($user);
            $item['index'] = $index + 1;
            $response[] = $item;
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
            'userData' => $userData ? [
                'lastName' => $userData->getLastName() ?? '',
                'firstName' => $userData->getFirstName() ?? '',
                'middleName' => $userData->getMiddleName() ?? '',
            ] : []
        ];
    }

    /**
     * @Route("/{id}", methods={"GET"}, name="fetch")
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
     * @Route("", methods={"POST"}, name="add")
     * @IsGranted("ROLE_ADMIN")
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
        if ($id) {
            if (!$user = $this->getDoctrine()->getRepository(User::class)->find($id)) {
                return new Response('', Response::HTTP_NOT_FOUND);
            }
        } else {
            $user = new User();
        }

        if (!$userData = $user->getUserData()) {
            $userData = new UserData();
            $user->setUserData($userData);
        }

        if (!$username = $request->request->get('username')) return new Response('Введите имя пользователя', Response::HTTP_BAD_REQUEST);
        if (!($password = $request->request->get('password')) && !$id) return new Response('Введите пароль', Response::HTTP_BAD_REQUEST);
        if (!($passwordConfirm = $request->request->get('confirmPassword')) && !$id && $password) return new Response('Введите подтверждение пароля', Response::HTTP_BAD_REQUEST);
        if ($password && ($password !== $passwordConfirm)) return new Response('Пароль и подтверждение пароля не совпадают', Response::HTTP_BAD_REQUEST);
        if (!$uData = $request->request->get('userData')) return new Response('Заполните личные данные пользователя', Response::HTTP_BAD_REQUEST);
        if (!$uData['lastName']) return new Response('Введите фамилию пользователя', Response::HTTP_BAD_REQUEST);
        if (!$uData['firstName']) return new Response('Введите имя пользователя', Response::HTTP_BAD_REQUEST);

        $user->setUsername($username)
             ->setPassword($this->passwordEncoder->encodePassword($user, $password));

        $userData->setLastName($uData['lastName'])->setFirstName($uData['firstName'])->setMiddleName($uData['middleName'] ?: null);

        $em = $this->getDoctrine()->getManager();

        $em->persist($user);
        $em->persist($userData);

        $em->flush();

        return new Response();
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
     * @Route("/{id}/toggle", methods={"POST"}, name="toggle")
     * @IsGranted("ROLE_ADMIN")
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
