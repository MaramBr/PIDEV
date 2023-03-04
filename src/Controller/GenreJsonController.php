<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GenreJsonController extends AbstractController
{
    #[Route('/genre/json', name: 'app_genre_json')]
    public function index(): Response
    {
        return $this->render('genre_json/index.html.twig', [
            'controller_name' => 'GenreJsonController',
        ]);
    }
}
