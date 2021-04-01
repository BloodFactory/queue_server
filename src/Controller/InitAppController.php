<?php

namespace App\Controller;

use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class InitAppController extends AbstractController
{
    /**
     * @Route("/init", methods={"GET"})
     * @IsGranted("ROLE_USER")
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        $userData = $user->getUserData();

        return $this->json([
            'user' => [
                'lastName' => $userData ? $userData->getLastName() : '',
                'firstName' => $userData ? $userData->getFirstName() : '',
                'middleName' => $userData ? $userData->getMiddleName() ?? '' : ''
            ],
            'ability' => $this->getAbilities()
        ]);
    }

    private function getAbilities(): array
    {
        $open = ['Homepage'];
        $add = [];
        $update = [];
        $delete = [];
        $toggle = [];

        if ($this->isGranted('ROLE_CLIENT')) {
            $open = array_merge($open, [
                'ClientHomepage',
                'Settings'
            ]);
            $add = array_merge($add, [
                'Settings',
            ]);
            $update = array_merge($update, [
                'Settings',
            ]);

            $toggle = array_merge($toggle, [
                'Settings',
            ]);

            $delete = array_merge($delete, [
                'Settings'
            ]);
        }

        if ($this->isGranted('ROLE_ADMIN')) {
            $open = array_merge($open, [
                'AdminHomepage',
                'Users',
                'Organizations',
            ]);

            $add = array_merge($add, [
                'Users',
                'Organizations',
            ]);

            $update = array_merge($update, [
                'Users',
                'Organizations',
            ]);

            $toggle = array_merge($toggle, [
                'Users',
            ]);
        }

        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            $add = array_merge($add, [
                'Admin'
            ]);

            $delete = array_merge($delete, [
                'Users',
                'Organizations'
            ]);
        }

        return [
            [
                'action' => 'open',
                'subject' => $open
            ],
            [
                'action' => 'add',
                'subject' => $add
            ],
            [
                'action' => 'update',
                'subject' => $update
            ],
            [
                'action' => 'delete',
                'subject' => $delete
            ],
            [
                'action' => 'toggle',
                'subject' => $toggle
            ]
        ];
    }
}
