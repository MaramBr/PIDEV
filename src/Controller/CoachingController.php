<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Coaching;//controller
use Doctrine\Persistence\ManagerRegistry;//controller
use App\Repository\CoachingRepository;//controller
use App\Form\CoachingType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class CoachingController extends AbstractController
{
    #[Route('/coaching', name: 'app_coaching')]
    public function index(): Response
    {
        return $this->render('coaching/index.html.twig', [
            'controller_name' => 'CoachingController',
        ]);
    }
    
    #[Route('/affichercoach', name: 'affichercoach')]
    public function affichercoach(ManagerRegistry $mg): Response
    {
        $repo=$mg->getRepository(Coaching::class);
        $resultat = $repo ->FindAll();
        return $this->render('coaching/afficherfront.html.twig', [
            'Coaching' => $resultat,
        ]);
    }

    
    #[Route('/afficherback', name: 'afficherback')]
    public function afficherback(ManagerRegistry $mg): Response
    {
        $repo=$mg->getRepository(Coaching::class);
        $resultat = $repo ->FindAll();
        return $this->render('coaching/afficherback.html.twig', [
            'Coaching' => $resultat,
        ]);
    }

    #[Route('/addcoach', name: 'addcoach')]
    public function addcoach(ManagerRegistry $doctrine,Request $request,SluggerInterface $slugger): Response
    {
        $Coaching =new Coaching();
        $Form=$this->createForm(CoachingType::class,$Coaching);
        $Form->handleRequest($request);
        if($Form->isSubmitted() && $Form->isValid()
)
        {
            /** @var UploadedFile $brochureFile */
            $brochureFile = $Form->get('imgCoach')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $brochureFile->move(
                        $this->getParameter('Coaching_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $Coaching->setImgCoach($newFilename);
            }
            $em=$doctrine->getManager();
            $em->persist($Coaching);
            $em->flush();
         return $this->redirectToRoute('afficherback');
        }
      //  return $this->render('productcontroller2/add.html.twig',array("form_student"=>$Form->createView()));
        return $this->renderForm('coaching/addC.html.twig',array("formCoaching"=>$Form));
    }

    #[Route('/updatecoach/{id}', name: 'updatecoach')]
    public function updatecoach(ManagerRegistry $doctrine,CoachingRepository $CoachingRepository,$id,Request $request): Response
    {
        $Coaching = $doctrine
        ->getRepository(Coaching::class)
        ->find($id);
        $form = $this->createForm(CoachingType::class, $Coaching);
        $form->add('update', SubmitType::class) ;
        $form->handleRequest($request);
        if ($form->isSubmitted())
        { $em = $doctrine->getManager();
            $em->flush();
            return $this->redirectToRoute('afficherback');
        }
        return $this->renderForm("coaching/addC.html.twig",
            ["formCoaching"=>$form]) ;
    }

    #[Route('/deletecoach/{id}', name: 'deletecoach')]
    public function deletecoach(ManagerRegistry $mg ,CoachingRepository $X , $id): Response
    {
        $Coaching= $X->find($id);
        $em=$mg->getManager();
        $em->remove($Coaching);
        $em->flush();
        return $this->redirectToRoute('afficherback');
    }
}
