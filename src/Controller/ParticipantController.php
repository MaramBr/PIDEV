<?php

namespace App\Controller;
use Symfony\Component\Mailer\Transport\Smtp\SmtpTransport;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Participant;//controller
use Doctrine\Persistence\ManagerRegistry;//controller
use App\Repository\ParticipantRepository;//controller
use Symfony\Component\Mailer\Transport;
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
    public function add(MailerInterface $mailer,ManagerRegistry $doctrine,Request $request): Response
    {
        $Participant=new Participant() ;
        //$participant = $this->getDoctrine()->getRepository(Participant::class)->find($id);
        $form=$this->createForm(ParticipantType::class,$Participant); //sna3na objet essmo form aamlena bih appel lel Participanttype
        $form->handleRequest($request);
        if( $form->isSubmitted() && $form->isValid() )  //amaalna verification esq taadet willa le aadna prob fi code ou nn
       {
        $em=$doctrine->getManager(); //appel lel manager
        $em->persist($Participant); //elli tzid
        $em->flush(); //besh ysob fi base de donnee
$this->addFlash(
                    'delP',
                    ' Participation Sauvgarder avec succés'
                );
       return $this->redirectToRoute('afficher', ['id' => $Participant->getId()]);
        
        }
        return $this->render('Participant/add3.html.twig', array("formParticipant"=>$form->createView()));
       // return $this->render('Participant/add.html.twig', array("formParticipant"=>$form->createView));



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
    {$c = $doctrine
        ->getRepository(Participant::class)
        ->find($id);
        $em = $doctrine->getManager();
        $em->remove($c);
        $em->flush() ;
        $this->addFlash(
                    'delP',
                    ' Participation supprimer avec succés'
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




    
}
