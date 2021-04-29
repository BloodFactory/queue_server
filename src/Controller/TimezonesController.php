<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/timezones", name="timezones_")
 * Class TimezonesController
 * @package App\Controller
 */
class TimezonesController extends AbstractController
{
    /**
     * @Route("", name="list")
     * @param Request $request
     * @return Response
     */
    public function fetchList(Request $request): Response
    {
        $timezones = timezone_identifiers_list();

        $search = $request->query->get('search');

        if ($search) {
            $timezones = array_filter($timezones, function ($val) use ($search) {
                return str_contains($val, $search);
            });
        }

        return $this->json($timezones);
    }
}
