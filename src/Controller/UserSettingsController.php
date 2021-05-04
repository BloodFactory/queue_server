<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserSettings;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserSettingsController extends AbstractController
{
    /**
     * @Route("/settings/darkMode", methods={"POST"})
     * @IsGranted("ROLE_USER")
     * @param Request $request
     * @return Response
     */
    public function toggleDarkMode(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();

        /** @var User $user */
        $user = $this->getUser();

        if (!($data = json_decode($request->getContent(), true)) || !isset($data['darkMode']) || !is_bool($data['darkMode'])) {
            return new Response('', Response::HTTP_BAD_REQUEST);
        }

        if (!$settings = $user->getUserSettings()) {
            $settings = new UserSettings();
            $user->setUserSettings($settings);
            $em->persist($user);
        }

        $settings->setDarkMode($data['darkMode']);

        $em->persist($settings);
        $em->flush();

        return new Response();
    }
}
