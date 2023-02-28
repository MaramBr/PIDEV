<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\RendezVous;//controller
use Doctrine\Persistence\ManagerRegistry;//controller
use App\Repository\RendezVousRepository;//controller
use App\Form\RendezVousType;
use App\Form\RendezVousType2;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Repository\CoachingRepository;//controller
use App\Entity\Coaching;//controller
use App\Form\CoachingType;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use MercurySeries\FlashyBundle\FlashyNotifier;




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
       /* $RendezVous->setCoachings($em->getRepository(Coaching::class)->find($request->get('Coachings')));        
       */ if($Form->isSubmitted() && $Form->isValid )
        {
            $em=$doctrine->getManager();
            $em->persist($RendezVous);
            $em->flush();
         return $this->redirectToRoute('afficherrdv');
        }
      //  return $this->render('productcontroller2/add.html.twig',array("form_student"=>$Form->createView()));
        return $this->renderForm('rendez_vous/addR.html.twig',array("formRendezVous"=>$Form));
    }
    #[Route('/addrdv2/{id}', name: 'addrdv2')]
    public function addrdv2(ManagerRegistry $doctrine,Request $request,  FlashyNotifier $flashy,LoggerInterface $logger, $id,MailerInterface $mailer): Response
    {   
        // $coaching = new Coaching();
        $repo=$doctrine->getRepository(Coaching::class);
        $coaching = $repo ->find($id);
        // $coaching->find($id);
        $RendezVous = new RendezVous();
        $Form=$this->createForm(RendezVousType2::class,$RendezVous);
        $Form->handleRequest($request);
        $RendezVous->setCoachings($coaching);

       /* $RendezVous->setCoachings($em->getRepository(Coaching::class)->find($request->get('Coachings')));        
       */ if($Form->isSubmitted() && $Form->isValid())
        {
            $em=$doctrine->getManager();
            $em->persist($coaching);
            $em->persist($RendezVous);
            $em->flush();
           
           $flashy->success('Réservation effectue!', 'http://your-awesome-link.com');
        //   $this->addFlash('message','Profil mis a jour');

         return $this->redirectToRoute('afficherrdv');
        }
      //  return $this->render('productcontroller2/add.html.twig',array("form_student"=>$Form->createView()));
        return $this->renderForm('rendez_vous/addR2.html.twig',array("formRendezVous"=>$Form));
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
    public function updaterdv(ManagerRegistry $doctrine,RendezVousRepository $RendezVousRepository ,$id,Request $request): Response
    {
        $RendezVous= $doctrine
        ->getRepository(RendezVous::class)
        ->find($id);
        $form = $this->createForm(RendezVousType::class, $RendezVous);
        $form->add('Modifier', SubmitType::class) ;
        $form->handleRequest($request);
        if ($form->isSubmitted())
        { $em = $doctrine->getManager();
            $em->flush();
            return $this->redirectToRoute('afficherrdv');
        }
        return $this->renderForm("rendez_vous/addR2.html.twig",
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

    #[Route('/email', name: 'email')]
    public function email(MailerInterface $mailer): Response
    {
        $content='<p>HTML body</p>';
        $email = (new Email())
            ->from('maram.brinsi@esprit.tn')
            ->to('maram.brinsi@esprit.tn')
            ->text('Body')
            ->subject('Test email')
            ->html('<p>Hello !</p>');

    
        $mailer->send($email);

        return $this->render('basefront.html.twig');
    }


/*

    #[Route('/notif', name: 'notif')]
      public function reserverSeanceCoaching(Request $request, NotificationService $notificationService)
    {
        // Créer un formulaire pour la réservation d'une séance de coaching
        $form = $this->createForm(ReservationType::class);
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer les données du formulaire
            $reservation = $form->getData();
            
            // Enregistrer la réservation dans la base de données
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($reservation);
            $entityManager->flush();
            
            // Envoyer une notification au coach
            $this->sendNotificationToCoach($reservation, $notificationService);
            
            // Rediriger l'utilisateur vers la page de confirmation
            return $this->redirectToRoute('confirmation_reservation');
        }
        
        return $this->render('reservation/reserver.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
    private function sendNotificationToCoach(RendezVous $reservation, NotificationService $notificationService)
    {
        $message = 'Nouvelle réservation effectuée par ' . $reservation->getCoaching()->getcours() . ' ' . $reservation->getClient()->getPrenom() . ' pour une séance de coaching avec vous.';
    
        // Envoyer la notification au coach
        $notificationService->sendNotification($reservation->getCoaching(), $message);
    }
*/
   






#[Route('/statrdv', name: 'statrdv', methods: ['GET'])]
public function statsrdv(CoachingRepository $coachingRepository): Response
{
    $entityManager = $this->getDoctrine()->getManager();
    $repository = $entityManager->getRepository(RendezVous::class);

    // Count total number of Coachings
    $totalRendezVous = $repository->createQueryBuilder('c')
        ->select('COUNT(c.id)')
        ->getQuery()
        ->getSingleScalarResult();

    // Query for all products and group them by category
    $query = $repository->createQueryBuilder('c')
        ->select('c.Coachings as Coaching, COUNT(c.id) as count, COUNT(c.id) / :total * 100 as percentage')
        ->setParameter('total', $totalRendezVous)
        ->groupBy('c.Coachings')
        ->getQuery();

    $Coachings = $query->getResult();

    // Calculate the counts array
    $counts = [];
    foreach ($Coachings as $RendezVous) {
        $counts[$RendezVous['Coaching']] = $RendezVous['count'];
    }

    return $this->render('rendez_vous/stats.html.twig', [
        'RendezVouss' => $Coachings,
        'counts' => $counts,
    ]);
}


#[Route('/notify', name: 'notify')]
public function notify(FlashyNotifier $flashy): Response
{
    $flashy->success('Réservation effectue!', 'http://your-awesome-link.com');

    return $this->render('rendez_vous/afficherrdv.html.twig');

    
}


#[Route('/listerdv/{id}', name: 'listerdv')]

    public function listerdv(Coaching $coaching): Response
    {
        $rdv = $coaching->getRendezVouses();
        return $this->render('coaching/listerdv.html.twig', [
            'coaching' => $coaching,
            'rdv' => $rdv,
        ]);
    }
}

