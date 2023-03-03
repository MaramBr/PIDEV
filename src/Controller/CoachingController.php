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
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Filesystem\Filesystem;
use App\Repository\RendezVousRepository;//controller
use App\Entity\RendezVous;//controller
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Notify;
use App\Repository\NotifyRepository;//controller


class CoachingController extends AbstractController
{
    #[Route('/coaching', name: 'app_coaching')]
    public function index(): Response
    {
        return $this->render('coaching/coachdetaille.html.twig', [
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


    #[Route('/afficherjson', name: 'affichercoachjson')]
    public function affichercoachjson(ManagerRegistry $mg,NormalizerInterface $normalizer): Response
    {
        $repo=$mg->getRepository(Coaching::class);
        $resultat = $repo ->FindAll();
        $coachingNormalises=$normalizer->normalize($resultat,'json',['groups'=>"Coaching"]);
        $json=json_encode($coachingNormalises);
        return new Response ($json);
    }
     
  

    /*
    #[Route('/afficherback', name: 'afficherback', methods: ['GET'])]
    public function afficherback(ManagerRegistry $mg,NormalizerInterface $normalizer,NotifyRepository $notify): Response
    {
        $repo=$mg->getRepository(Coaching::class);
        $resultat = $repo ->FindAll();
        $notif=$notify->FindAll();
        $coachingNormalises=$normalizer->normalize($resultat,'json',['groups'=>"Coaching","Notify"]);
        $json=json_encode($coachingNormalises);

        return $this->render('coaching/afficherback.html.twig', [
            'Coaching' => $resultat,
            'notify' => $notify,
        ]);
    }
*/
    #[Route('/afficherback', name: 'afficherback', methods: ['GET'])]

    public function affiche1(ManagerRegistry $mg,NotifyRepository $notify): Response
    {
        $repo=$mg->getRepository(Coaching::class);
        $notify=$mg->getRepository(Notify::class);

        $result=$repo->findAll();
        $notif=$notify->FindAll();
        return $this->render ('coaching/afficherback.html.twig',['Coaching'=>$result
                                                                ,'notif'=>$notif
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

         return $this->redirectToRoute('afficherback');
        }
      //  return $this->render('RendezVouscontroller2/add.html.twig',array("form_Coaching"=>$Form->createView()));
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
        return $this->render('coaching/coachdetaille.html.twig', [
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

   
   


    #[Route('/ajoutjson', name: 'ajoutjson')]
    public function ajoutjson(ManagerRegistry $doctrine,Request $request,NormalizerInterface $normalizer): Response
    {
        $Coaching =new Coaching();
        $cours=$request->query->get('cours');
        $dispocoach=$request->query->get('dispoCoash');
        $img=$request->query->get('imgCoach');
        $em=$doctrine->getManager();

        $Coaching->setCours('cours');
        $Coaching->setDispoCoach('dispocoach');
        $Coaching->setimgCoach('img');

        $em->persist($Coaching);
        $em->flush();

        $serializer =new Serializer([new ObjectNormalizer()]);
        $formatted=$serializer->normalize($Coaching);

        return new JsonResponse ($formatted);
    }

    #[Route('/stat', name: 'stat', methods: ['GET'])]
    public function Coachingstats(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $repository = $entityManager->getRepository(Coaching::class);
    
        // Count total number of Coachings
        $totalCoachings = $repository->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->getQuery()
            ->getSingleScalarResult();
    
        // Query for all RendezVouss and group them by Coaching
        $query = $repository->createQueryBuilder('c')
            ->select('c.cours as cours, COUNT(c.id) as count, COUNT(c.id) / :total * 100 as percentage')
            ->setParameter('total', $totalCoachings)
            ->groupBy('c.cours')
            ->getQuery();
    
        $Coachings = $query->getResult();
    
        
    
        // Calculate the counts array
        $counts = [];
        foreach ($Coachings as $Coaching) {
            $counts[$Coaching['cours']] = $Coaching['count'];
        }
    
        return $this->render('coaching/stats.html.twig', [
            'Coachings' => $Coachings,
            'counts' => $counts,
        ]);
    }





    #[Route('/notifyy', name: 'notifyy')]
    public function notifyy(FlashyNotifier $flashy): Response
    {
        $flashy->success('Réservation effectue!', 'http://your-awesome-link.com');

        return $this->render('coaching/home.html.twig');

        
    }


    #[Route('/listecoach', name: 'listecoach')]
    public function listecoachy($id, CoachingRepository $CoachingRepository, RendezVousRepository $RendezVousRepository): Response
    {
        $Coaching = $CoachingRepository->find($id);

        if (!$Coaching) {
            throw $this->createNotFoundException('La seance de coaching demandée n\'existe pas.');
        }

        $RendezVous = $RendezVousRepository->findByCoaching($Coaching);

        return $this->render('coaching/listerdv.html.twig', [
            'Coaching' => $Coaching,
            'RendezVous' => $RendezVous,
        ]);
    }





    #[Route('/updatejson/{id}', name: 'updatejson')]

    public function updatejson(Request $req, $id, NormalizerInterface $Normalizer)
    {
               $em = $this->getDoctrine()->getManager();
               $Coaching= $em->getRepository(Coaching::class)->find($id);
               $Coaching->setCours('cours');
$Coaching->setDispoCoach('dispocoach');
$Coaching->setimgCoach('img');

$em->persist($Coaching);
               $em->flush();
               $jsonContent=$Normalizer->normalize($Coaching, 'json', ['groups' => 'Coachings']);
return new Response("Coachingupdated successfully" .json_encode ($jsonContent));
    }

    

    #[Route('/deletejson/{id}', name: 'deletejson')]

    public function deletejson(Request $request, $id)
    {           $id=$request->get('id');
               $em = $this->getDoctrine()->getManager();
               $Coaching= $em->getRepository(Coaching::class)->find($id);
               

                $em->remove($Coaching);
                $em->flush();

            
               $jsonContent=$Normalizer->normalize($Coaching, 'json', ['groups' => 'Coachings']);
return new Response("Coaching deleted successfully" .json_encode ($jsonContent));
    }


    #[Route('/like/{id}', name: 'like', methods: ['POST'])]
public function likeCoaching(Request $request, Coaching $Coaching): Response
{
    $entityManager = $this->getDoctrine()->getManager();
    $Coaching->setLikeButton($Coaching->getLikeButton() + 1);
    if ( $Coaching->setLikeButton($Coaching->getLikeButton() == 1) )

            $Coaching->getDislikeButton() == 0  ;

    $entityManager->persist($Coaching);
    $entityManager->flush();

    return $this->redirectToRoute('detaille', ['id' => $Coaching->getId()]);
}


    

#[Route('/dislike/{id}', name: 'dislike', methods: ['POST'])]

public function dislikeCoaching(Request $request, Coaching $Coaching): Response
{
    $entityManager = $this->getDoctrine()->getManager();
    $Coaching->setDislikeButton($Coaching->getDislikeButton() + 1);

  if(  $Coaching->setDislikeButton($Coaching->getDislikeButton() == 1))
       { $Coaching->getLikeButton() == 0 ;}
    $entityManager->persist($Coaching);
    $entityManager->flush();

    return $this->redirectToRoute('detaille', ['id' => $Coaching->getId()]);
}



    #[Route('/order_By_Nom', name: 'order_By_Nom', methods: ['GET'])]

    public function order_By_Nom(Request $request,CoachingRepository $CoachingRepository): Response
    {
//list of students order By Dest
        $CoachingByNom = $CoachingRepository->order_By_Nom();

        return $this->render('coaching/afficherfront.html.twig', [
            'Coaching' => $CoachingByNom,
        ]);

        //trie selon Date normal

    }
   
}
