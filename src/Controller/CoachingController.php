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
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

use Symfony\Component\Serializer\Annotation\Groups;



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
    public function affichercoach(ManagerRegistry $mg,NormalizerInterface $normalizer): Response
    {
        $repo=$mg->getRepository(Coaching::class);
        $resultat = $repo ->FindAll();
        $coachingNormalises=$normalizer->normalize($resultat,'json',['groups'=>"Coaching"]);
        $json=json_encode($coachingNormalises);
        return $this->render('coaching/afficherfront.html.twig', [
            'Coaching' => $resultat,
        ]);
    }
     
  

    
    #[Route('/afficherback', name: 'afficherback')]
    public function afficherback(ManagerRegistry $mg,NormalizerInterface $normalizer): Response
    {
        $repo=$mg->getRepository(Coaching::class);
        $resultat = $repo ->FindAll();
        $coachingNormalises=$normalizer->normalize($resultat,'json',['groups'=>"Coaching"]);
        $json=json_encode($coachingNormalises);

        return $this->render('coaching/afficherback.html.twig', [
            'Coaching' => $resultat,
        ]);
    }

    #[Route('/addcoach', name: 'addcoach')]
    public function addcoach(ManagerRegistry $doctrine,Request $request,SluggerInterface $slugger,NormalizerInterface $normalizer): Response
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
            $coachingNormalises=$normalizer->normalize($Coaching,'json',['groups'=>"Coaching"]);

         return $this->redirectToRoute('afficherback');
        }
      //  return $this->render('productcontroller2/add.html.twig',array("form_student"=>$Form->createView()));
        return $this->renderForm('coaching/addC.html.twig',array("formCoaching"=>$Form));
    }

    #[Route('/updatecoach/{id}', name: 'updatecoach')]
    public function updatecoach(ManagerRegistry $doctrine,NormalizerInterface $normalizer,CoachingRepository $CoachingRepository,$id,Request $request): Response
    {
        $Coaching = $doctrine
        ->getRepository(Coaching::class)
        ->find($id);
        $form = $this->createForm(CoachingType::class, $Coaching);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        { $em = $doctrine->getManager();
            $em->flush();
            $coachingNormalises=$normalizer->normalize($Coaching,'json',['groups'=>"Coaching"]);

            return $this->redirectToRoute('afficherback');
        }
        return $this->renderForm("coaching/addC.html.twig",
            ["formCoaching"=>$form]) ;
    }

    #[Route('/deletecoach/{id}', name: 'deletecoach')]
    public function deletecoach(ManagerRegistry $mg ,NormalizerInterface $normalizer,CoachingRepository $X , $id): Response
    {
        $Coaching= $X->find($id);
        $em=$mg->getManager();
        $em->remove($Coaching);
        $em->flush();
        $coachingNormalises=$normalizer->normalize($Coaching,'json',['groups'=>"Coaching"]);

        return $this->redirectToRoute('afficherback');
    }

    #[Route('/detaille/{id}', name: 'detaille')]
    public function detaille($id,ManagerRegistry $mg,NormalizerInterface $normalizer, LoggerInterface $logger): Response
    {
        $repo=$mg->getRepository(Coaching::class);
        $resultat = $repo ->find($id);
        $logger->info("The array is: " . json_encode($resultat));
        return $this->render('coaching/index.html.twig', [
            'Coaching' => $resultat,
        ]);
    }


    #[Route('/ajax', name: 'ajax')]
    public function ajax(Request $request,NormalizerInterface $Normalizer,CoachingRepository $sr,LoggerInterface $logger): Response
    {
        $logger->info("Bonjour");

        $repository=$this->getDoctrine()->getRepository(Coaching::class);
        $requestString=$request->get('term');
        $logger->info("The ajax is: " .$requestString);

        $Coaching=$sr->findCoachingByCours($requestString);

        $logger->info("maram: " .json_encode($Coaching));
        $jsonContent=$Normalizer->normalize($Coaching, 'json' ,['groups'=>'Coaching']);
        $retour=json_encode($jsonContent);
        return new Response($retour);
        
    }

   
   

   
    
}
