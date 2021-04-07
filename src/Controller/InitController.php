<?php

namespace App\Controller;

use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InitController extends AbstractController
{
    /**
     * @Route("/init", methods={"GET"})
     * @IsGranted("ROLE_USER")
     * @return Response
     */
    public function init(): Response
    {
        /** @var User $_user */
        $_user = $this->getUser();

        $user = [
            'username' => $_user->getUsername(),
        ];

        if ($organization = $_user->getOrganization()) {
            $user['organization'] = [
                'id' => $organization->getId(),
                'name' => $organization->getName()
            ];
        }
        return $this->json([
            'user' => $user,
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
                'Services'
            ]);

            $add = array_merge($add, [
                'Users',
                'Organizations',
                'Services'
            ]);

            $update = array_merge($update, [
                'Users',
                'Organizations',
                'Services'
            ]);

            $toggle = array_merge($toggle, [
                'Users',
                'Services'
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
