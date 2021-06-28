<?php

namespace App\Controller;

use App\Entity\Registration;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/registrations", name="registrations_")
 */
class RegistrationsController extends AbstractController
{
    /**
     * @Route("", name="list", methods={"GET"})
     * @IsGranted("ROLE_CLIENT")
     * @param Request $request
     * @return Response
     */
    public function fetchList(Request $request): Response
    {
        $appointmentId = $request->query->getInt('appointmentID');

        $result = $this->getDoctrine()
                       ->getRepository(Registration::class)
                       ->createQueryBuilder('r')
                       ->andWhere('r.appointment = :appointment')
                       ->setParameter('appointment', $appointmentId)
                       ->getQuery()
                       ->getArrayResult();

        return $this->json($result);
    }
}
