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
use App\Entity\Notify;





class RendezVousController extends AbstractController
{
    #[Route('/rendez/vous', name: 'app_rendez_vous')]
    public function index(): Response
    {
        return $this->render('rendez_vous/calendar.html.twig', [
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
        $notify=$mg->getRepository(Notify::class);

        $resultat = $repo ->FindAll();
        $notif=$notify->FindAll();
        return $this->render ('rendez_vous/afficherback.html.twig',['rdv'=>$resultat
                                                                ,'notif'=>$notif
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
    {           $utilisateur = $this->getUser();
        // dd($utilisateur);
        if(!$utilisateur)
{
    return $this->redirectToRoute('app_login');

}
else{
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
    }

    
    #[Route('/getByCours/{cours}', name: 'appgetByCours')]
    public function getByCours(ManagerRegistry $doctrine,Request $request, $cours, CoachingRepository $coachingRepository): Response
    {        $coaching = $coachingRepository->findByCours($cours);
        if (!$coaching) {
            throw $this->createNotFoundException('Coaching not found for cours: '.$cours);
        }

        return $this->render('coaching/coachdetaille.html.twig', [
            'Coaching' => $coaching,
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

    #[Route('/email/{id}', name: 'emailcoach')]
    public function email(MailerInterface $mailer,ManagerRegistry $mg,RendezVousRepository $X,$id): Response
    {

        
        $RendezVous=$X->find($id);
        $RendezVous->setEtatrdv('true');

$user=$this->getUser();

        $content='<p>HTML body</p>';

        $email = (new Email())
            ->from('maram.brinsi@esprit.tn')
            ->to($user->getEmail())
            ->text('Body')
            ->subject('Séance de Coaching')
            ->html('Votre réservation a été confirmer');
            $repo=$mg->getRepository(RendezVous::class);
            $resultat = $repo ->FindAll();

    
        $mailer->send($email);

        $em=$mg->getManager();
        $em->persist($RendezVous);
         $em->flush();
        return $this->render('rendez_vous/afficherback.html.twig', [
            'rdv' => $resultat,
        ]);

      //  return $this->render('rendez_vous/afficherback.html.twig');
    }







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
        ->select('c.Id as Coachings, COUNT(c.id) as count, COUNT(c.id) / :total * 100 as percentage')
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
/*
#[Route('/calendar', name: 'calendar')]

public function onCalendarSetData(RendezVous $calendar)
    {
        $start = $calendar->getStart();
        $end = $calendar->getEnd();
        $filters = $calendar->getFilters();

        // You may want to make a custom query from your database to fill the calendar

        $calendar->addRendezVous(new RendezVous(
            'RendezVous 1',
            new \DateTime('Tuesday this week'),
            new \DateTime('Wednesdays this week')
        ));

        // If the end date is null or not defined, it creates a all day RendezVous
        $calendar->addRendezVous(new RendezVous(
            'All day RendezVous',
            new \DateTime('Friday this week')
        ));
    }

*/
#[Route('/listerdv/{id}', name: 'listerdv')]

    public function listerdv(Coaching $coaching): Response
    {
        $rdv = $coaching->getRendezVouses();
        return $this->render('coaching/listerdv.html.twig', [
            'coaching' => $coaching,
            'rdv' => $rdv,
        ]);
    }


    
    #[Route('/calendarCoach', name: 'calendarCoach')]

    public function calendar(ManagerRegistry $doctrine): Response
    {

        $RendezVouss = $doctrine->getRepository(RendezVous::class)->findAll();
        $rdvs = [];

        foreach($RendezVouss as $RendezVous){
          
            $rdvs[] = [
                'id'=>$RendezVous->getId(),
                'title' => $RendezVous->getCoachings()->getCours(),
                'date' => $RendezVous->getDaterdv()->format('Y-m-d'),
            ];
        }
        $data = json_encode($rdvs);
      
        return $this->render('rendez_vous/calendar.html.twig'          
        , compact('data'));
    }

   /* #[Route('/calendar/{id}', name: 'rdv_deplacer',methods:['POST'])]

     public function deplacerrdv(Request $request, RendezVous $rdv, ManagerRegistry $doctrine): JsonResponse
     {
        $donnees = json_decode($request->getContent());
        
         $start = $donnees->date;
        //  dump("$start");
        //  VarDumper::dump($start);

         $now = new DateTimeImmutable($start);
        //$newDate = $now->setDate(2023, 4, 1);
        
         
         $rdv->setDaterdvv($now);
        


         $entityManager=$doctrine->getManager();
         $entityManager->flush();
     
         return new JsonResponse(['success' => true]);
     }*/
     #[Route('/order_By_Date', name: 'order_By_Date',methods:['GET'])]

     public function order_By_Date(Request $request,RendezVousRepository $RendezVousRepository): Response
     {
 //list of students order By Dest
         $RendezVousByDate = $RendezVousRepository->order_By_Date();
 
         return $this->render('rendez_vous/afficherfront.html.twig', [
             'rdv' => $RendezVousByDate,
         ]);
 
         //trie selon Prix
 
     }
     
     #[Route('/order_By_Nom', name: 'order_By_Nom',methods:['GET'])]
 
 
     public function order_By_Nom(Request $request,RendezVousRepository $RendezVous): Response
     {
 //list of students order By Dest
         $RendezVousByNom = $RendezVous->order_By_Nom();
 
         return $this->render('rendez_vous/afficherfront.html.twig', [
             'rdv' => $RendezVousByNom,
         ]);
 
         //trie selon Date normal
 
     }

     #[Route('/statistics', name: 'RendezVous_statistics')]
public function statistics(ManagerRegistry $doctrine): Response {
    $em = $doctrine->getManager();
    $RendezVousRepository = $em->getRepository(RendezVous::class);
    $notify=$doctrine->getRepository(Notify::class);
    $notif=$notify->FindAll();


    // Get the total number of RendezVouss
    $totalRendezVouss = $RendezVousRepository->createQueryBuilder('u')
        ->select('COUNT(u.id)')
        ->getQuery()
        ->getSingleScalarResult();

    // Get the number of RendezVouss per role
    $RendezVousCoachings = $RendezVousRepository->createQueryBuilder('r')
    ->join('r.Coachings', 'c')
    ->select('c.cours', 'COUNT(r.id) AS RendezVousCount')
    ->groupBy('c.cours')
    ->getQuery()
    ->getResult();

    // ...rest of the code

    return $this->render('rendez_vous/stats.html.twig', [
        'totalRendezVouss' => $totalRendezVouss,
        'RendezVousCoachings' => $RendezVousCoachings,
        'notif'=>$notif
    ]);
}



}