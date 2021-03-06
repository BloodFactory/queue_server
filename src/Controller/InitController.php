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
     * @Route("/init", methods={"GET"}, name="init_app")
     * @IsGranted("ROLE_USER")
     * @return Response
     */
    public function init(): Response
    {
        $result = [
            'ability' => $this->getAbilities()
        ];

        /** @var User $user */
        $user = $this->getUser();

        $result['user']['username'] = $user->getUsername();

        if ($userData = $user->getUserData()) {
            $result['user']['lastName'] = $userData->getLastName();
            $result['user']['firstName'] = $userData->getFirstName();
            $result['user']['middleName'] = $userData->getMiddleName();
        }

        if ($settings = $user->getUserSettings()) {
            $result['settings']['darkMode'] = $settings->getDarkMode();
        }

        if ($user->getUserRights()->count() > 0) {
            $rights = [];
            foreach ($user->getUserRights() as $userRights) {
                $rights[$userRights->getOrganization()->getId()] = [
                    'view' => $userRights->getView(),
                    'edit' => $userRights->getEdit(),
                    'delete' => $userRights->getDelete()
                ];
            }

            $result['rights'] = $rights;
        }

        return $this->json($result);
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
                'Settings',
                'Appointments'
            ]);
            $add = array_merge($add, [
                'Settings',
                'Appointments'
            ]);
            $update = array_merge($update, [
                'Settings',
                'Appointments'
            ]);

            $toggle = array_merge($toggle, [
                'Settings',
                'Appointments'
            ]);

            $delete = array_merge($delete, [
                'Settings',
                'Appointments'
            ]);
        }

        if ($this->isGranted('ROLE_ADMIN')) {
            $open = array_merge($open, [
                'AdminHomepage',
                'Users',
                'Organizations',
                'Services',
            ]);

            $add = array_merge($add, [
                'Users',
                'Organizations',
                'Services',
            ]);

            $update = array_merge($update, [
                'Users',
                'Organizations',
                'Services',
            ]);

            $delete = array_merge($delete, [
                'Services',
            ]);

            $toggle = array_merge($toggle, [
                'Users',
                'Services',
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
