<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ExceptionController extends AbstractController
{
    #[Route('/403', name: 'not_found')]
    public function notFound(): Response
    {
        return $this->render('/403.html.twig');
    }
}
