<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Genre;
use App\Form\GenreType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\GenreRepository;


class GenreController extends AbstractController
{
    #[Route('/genre', name: 'app_genre')]
    public function index(): Response
    {
        return $this->render('genre/index.html.twig', [
            'controller_name' => 'GenreController',
        ]);
    }
        //////////////////////////ajouter////////////////////////
        #[Route('/addgenre', name: 'addgenre')]
        public function addgenre (ManagerRegistry $doctrine,Request $request): Response
        {   $Genre =new Genre();
            $Form=$this->createForm(GenreType::class,$Genre);
            $Form->handleRequest($request);
            if($Form->isSubmitted() && $Form->isValid())
            {
                $em=$doctrine->getManager();
                $em->persist($Genre);
                $em->flush();
             return $this->redirectToRoute('affgenre');
            }
          //  return $this->render('productcontroller2/add.html.twig',array("form_student"=>$Form->createView()));
            return $this->renderForm('genre/index.html.twig',array("form1"=>$Form));
        }
    //////////////////////////afficher///////////////////////
        #[Route('/affichergenre', name: 'affgenre')]
          public function liste (ManagerRegistry $mg): Response
          {
              $repo=$mg->getRepository(Genre::class);
              $resultat = $repo ->FindAll();
              return $this->render('genre/afficherback.html.twig', [
                  'genres' => $resultat,
              ]);
          } 
    //////////////////////////modifier////////////////////////
          #[Route('/updategenre/{id}', name: 'app_updateg')]
           public function updateGenre(ManagerRegistry $doctrine,GenreRepository $GenreRepository,$id,Request $request): Response
           {
          $Genre=$GenreRepository->find($id);
          $form=$this->CreateForm(GenreType :: class,$Genre);
          $form->handleRequest($request);
          if($form->isSubmitted())
          {
          $em=$doctrine->getManager();
          $em->persist($Genre);
          $em->flush();
        return $this->redirectToRoute('affgenre');
          }
          return $this->renderForm('genre/index.html.twig',array("form1"=>$form));
       
           }
    
    //////////////////////////supprimer////////////////////////
    #[Route('/removegenre/{id}', name: 'removegenre')]
    public function remove (ManagerRegistry $mg ,GenreRepository $X , $id): Response
    {    
      
      $Genre= $X->find($id);
      $em=$mg->getManager();
      $em->remove($Genre);
      $em->flush();
       return $this->redirectToRoute('affgenre');
    }       
    
}
