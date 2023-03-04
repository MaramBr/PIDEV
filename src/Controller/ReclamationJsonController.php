<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReclamationJsonController extends AbstractController
{
    #[Route('/reclamation/json', name: 'app_reclamation_json')]
    public function index(): Response
    {
        return $this->render('reclamation_json/index.html.twig', [
            'controller_name' => 'ReclamationJsonController',
        ]);
    }
}
