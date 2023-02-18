<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\RendezVous;//controller
use Doctrine\Persistence\ManagerRegistry;//controller
use App\Repository\RendezVousRepository;//controller
use App\Form\RendezVousType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Repository\CoachingRepository;//controller
use App\Entity\Coaching;//controller
use App\Form\CoachingType;


class RendezVousController extends AbstractController
{
    #[Route('/rendez/vous', name: 'app_rendez_vous')]
    public function index(): Response
    {
        return $this->render('rendez_vous/index.html.twig', [
            'controller_name' => 'RendezVousController',
        ]);
    }

    #[Route('/afficherrdv', name: 'afficherrdv')]
    public function afficherrdv(ManagerRegistry $mg): Response
    {
        $repo=$mg->getRepository(RendezVous::class);
        $resultat = $repo ->FindAll();
        return $this->render('rendez_vous/afficherfront.html.twig', [
            'rdv' => $resultat,
        ]);
    }

    #[Route('afficherrdvback', name: 'afficherrdvback')]
    public function afficherrdvback(ManagerRegistry $mg): Response
    {
        $repo=$mg->getRepository(RendezVous::class);
        $resultat = $repo ->FindAll();
        return $this->render('rendez_vous/afficherback.html.twig', [
            'rdv' => $resultat,
        ]);
    }

    #[Route('/addrdv', name: 'addrdv')]
    public function addrdv(ManagerRegistry $doctrine,Request $request): Response
    {
        $RendezVous =new RendezVous();
        $Form=$this->createForm(RendezVousType::class,$RendezVous);
        $Form->handleRequest($request);
        if($Form->isSubmitted())
        {
            $em=$doctrine->getManager();
            $em->persist($RendezVous);
            $em->flush();
         return $this->redirectToRoute('/getByCours/Fitness');
        }
      //  return $this->render('productcontroller2/add.html.twig',array("form_student"=>$Form->createView()));
        return $this->renderForm('rendez_vous/addR.html.twig',array("formRendezVous"=>$Form));
    }

    
    #[Route('/getByCours/{cours}', name: 'appgetByCours')]
    public function getByCours(ManagerRegistry $doctrine,Request $request, $cours, CoachingRepository $coachingRepository): Response
    {        $coaching = $coachingRepository->findByCours($cours);
        if (!$coaching) {
            throw $this->createNotFoundException('Coaching not found for cours: '.$cours);
        }

        return $this->render('rendez_vous/liste.html.twig', [
            'coaching' => $coaching,
        ]);
    }

    

    #[Route('/updaterdv/{id}', name: 'updaterdv')]
    public function updaterdv(ManagerRegistry $doctrine,RendezVousRepository $RendezVousRepository,$id,Request $request): Response
    {
        $RendezVous= $doctrine
        ->getRepository(RendezVous::class)
        ->find($id);
        $form = $this->createForm(RendezVousType::class, $RendezVous);
        $form->add('update', SubmitType::class) ;
        $form->handleRequest($request);
        if ($form->isSubmitted())
        { $em = $doctrine->getManager();
            $em->flush();
            return $this->redirectToRoute('afficherrdv');
        }
        return $this->renderForm("rendez_vous/addR.html.twig",
            ["formRendezVous"=>$form]) ;
    }

    #[Route('/deleterdv/{id}', name: 'deleterdv')]
    public function deleterdv(ManagerRegistry $mg ,RendezVousRepository $X , $id): Response
    {
        $RendezVous= $X->find($id);
        $em=$mg->getManager();
        $em->remove($RendezVous);
        $em->flush();
        return $this->redirectToRoute('afficherrdv');
    }


    #[Route('/comparer', name: 'comparer')]
    public function comparer(Request $request, CoachingRepository $coachingRepository): Response
    {
        $rendezVous = new RendezVous();
        $form = $this->createForm(RendezVousType::class, $rendezVous);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cours = $rendezVous->getCours();
            $dispo = $rendezVous->getDate();

            $coaches = $coachingRepository->findAvailableCoachesForCourseAndDate($cours, $dispo);

            return $this->render('rendez_vous/result.html.twig', [
                'coaches' => $coaches,
            ]);
        }

        return $this->render('rendez_vous/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

   
}

