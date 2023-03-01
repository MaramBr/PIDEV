<?php

namespace App\Controller;
use Symfony\Component\Mailer\Transport\Smtp\SmtpTransport;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Participant;
use App\Entity\Evenement;//controller
use Doctrine\Persistence\ManagerRegistry;//controller
use App\Repository\ParticipantRepository;//controller
use Symfony\Component\Mailer\Transport;
use App\Repository\EvenementRepository;
use Symfony\Component\Mailer\Transport\Smtp\Stream\SocketStream;
use App\Form\ParticipantType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
//use Symfony\Component\Validator\Constraints as Assert;
// use Symfony\Component\Validator\Constraints\Email;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Session\Session;
use App\Form\ForgotPasswordType;
use App\Repository\UserRepository;
use App\Form\UserType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;
use App\Form\RegistrationFormType;
use Swift_Mailer;
use Swift_Message;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use PHPMailer\PHPMailer\PHPMailer;





use Twilio\Rest\Client;

 
class ParticipantController extends AbstractController
{
    #[Route('/Participant', name: 'app_Participant')]
    public function index(): Response
    {
        return $this->render('Participant/index.html.twig', [
            'controller_name' => 'ParticipantController',
        ]);
    }

   
    
    #[Route('/Participant/afficher', name: 'appback3')]
    public function afficheback(ManagerRegistry $em): Response
    {
        $repo=$em->getRepository(Participant::class);
        $result=$repo->findAll();
        return $this->render ('Participant/annuler.html.twig',['Participant'=>$result]);
   
       
    }
 
    #[Route('/Participant/afficherback', name: 'affichefront1')]
    public function affiche6(ManagerRegistry $em): Response
    {
        $repo=$em->getRepository(Participant::class);
        $result=$repo->findAll();
        return $this->render ('Participant/back3.html.twig',['Participant'=>$result]);
   
       
    }



   #[Route('/Participant/add', name: 'add3')]
public function add(MailerInterface $mailer, ManagerRegistry $doctrine, Request $request): Response
{
    $participant = new Participant();
    $form = $this->createForm(ParticipantType::class, $participant);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $em = $doctrine->getManager();

        // Get the events associated with the participation
        $events = $form->get('evenement')->getData();

        foreach ($events as $evenement) {

            // Check if the number of participants for the event is greater than zero
            if ($evenement->getNbParticipant() <= 0) {
                $this->addFlash(
                    'error',
                    'Impossible d\'ajouter un participant à l\'événement ' . $evenement->getNom() . ' car le nombre maximum de participants est atteint.'
                );
                return $this->redirectToRoute('add3');
            }
            // Update the number of participants for the event
            $evenement->setNbParticipant($evenement->getNbParticipant() - 1);

            // Associate the participant with the event
            $participant->addEvenement($evenement);

            // Save the event to the database
            $em->persist($evenement);
        }

        // Save the participant to the database
        $em->persist($participant);
        $em->flush();

        $this->addFlash(
            'addP',
            'Participation sauvegardée avec succès.'
        );

        return $this->redirectToRoute('afficher', ['id' => $participant->getId()]);
    }

    return $this->render('Participant/add3.html.twig', [
        'formParticipant' => $form->createView(),
    ]);
}

     
   

    #[Route('/Participant/update/{id}', name: 'update3')]

    public function  updateParticipant (ManagerRegistry $doctrine,$id,  Request  $request) : Response
    { $Participant = $doctrine

        ->getRepository(Participant::class)
        ->find($id);
        $form = $this->createForm(ParticipantType::class, $Participant);
        $form->add('update', SubmitType::class) ;
        $form->handleRequest($request);
        if ($form->isSubmitted())
        { $em = $doctrine->getManager();
            $em->flush();
           
       return $this->redirectToRoute('afficher', ['id' => $Participant->getId()]);
        }
        return $this->renderForm("Participant/add3.html.twig",
            ["formParticipant"=>$form]) ;


    } 

    #[Route('/Participant/delete/{id}', name: 'delete3')]

    public function delete($id, ManagerRegistry $doctrine)
{
    $participant = $doctrine
        ->getRepository(Participant::class)
        ->find($id);

    $em = $doctrine->getManager();
    $em->remove($participant);

    // Get the events associated with the participation
    $events = $participant->getEvenement();

    foreach ($events as $evenement) {
        // Increment the nbParticipant attribute of the event
        $evenement->setNbParticipant($evenement->getNbParticipant() + 1);

        // Save the event to the database
        $em->persist($evenement);
    }

    $em->flush();

    $this->addFlash(
        'delP',
        'Participation supprimée avec succès'
    );

    return $this->redirectToRoute('appback3');
}


    #[Route('/Participant/delete2/{id}', name: 'delete5')]

    public function delete2($id, ManagerRegistry $doctrine)
    {$c = $doctrine
        ->getRepository(Participant::class)
        ->find($id);
        $em = $doctrine->getManager();
        $em->remove($c);
        $em->flush() ;
        return $this->redirectToRoute('affichefront1');
    }


 
  #[Route('/Participant/affiche/{id}', name: 'afficher')]
 public function showAction(int $id): Response
{
    $participant = $this->getDoctrine()->getRepository(Participant::class)->find($id);

    if (!$participant) {
        throw $this->createNotFoundException('Participant introuvable');
    }

    return $this->render('participant/affich.html.twig', [
        'participant' => $participant,
    ]);
}

 
#[Route('/pdf', name:'export_pdf', methods: ["GET"])]
     
    public function pdf(ParticipantRepository $ParticipantRepository, Dompdf $dompdf)
    {
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('evenement/pdf.html.twig', [
            'participant' => $ParticipantRepository->findAll(),
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (inline view)
        $dompdf->stream("ListeDesParticipants.pdf", [
            "participant" => true
        ]);
    }

  
}
