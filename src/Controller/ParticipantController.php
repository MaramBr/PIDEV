<?php

namespace App\Controller;
use Symfony\Component\Mailer\Transport\Smtp\SmtpTransport;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Participant;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\HttpFoundation\JsonResponse;
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
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
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

    #[Route('/Participant/ajoutjson1', name: 'ajoutjsonp')]
    public function ajoutjson(ManagerRegistry $doctrine, Request $request, NormalizerInterface $normalizer): Response
    {
        $nom = $request->query->get('nom');
        $prenom = $request->query->get('prenom');
        $email = $request->query->get('email');
        $tel = $request->query->get('tel');
        $age = $request->query->get('age');
    
        $em = $doctrine->getManager();
        $Participant = new Participant();
    
        if ($nom !== null) {
            $Participant->setNom($nom);
        } else {
            $Participant->setNom('');
        }
        if ($prenom !== null) {
            $Participant->setPrenom($prenom);
        } else {
            $Participant->setPrenom('');
        }
        if ($email !== null) {
            $Participant->setEmail($email);
        } else {
            $Participant->setEmail('');
        }
        $Participant->setTel(intval($tel));
        $Participant->setAge(intval($age));
    
       
    
        $em->persist($Participant);
        $em->flush();
    
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($Participant);
    $accountSid = 'AC92f7e404547cf2736427dd218cd01a28';
            $authToken = '592b08daa6c3bc91cfbc6f994af97d48';
            $client = new Client($accountSid, $authToken);
    
            $message = $client->messages->create(
                '+21627085182', // replace with admin's phone number
                [
                    'from' => '+12764009477', // replace with your Twilio phone number
                    'body' => 'un nouveau participant ' ,
                ]
            );
        return new JsonResponse($formatted);
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

       /* //////////////////////SMS////////////////////////////////
          
 $accountSid = 'AC92f7e404547cf2736427dd218cd01a28';
            $authToken = '6a04339c5caf500fb20fe60528a1b772';
            $client = new Client($accountSid, $authToken);
    
            $message = $client->messages->create(
                '+21627085182', // replace with admin's phone number
                [
                    'from' => '+12764009477', // replace with your Twilio phone number
                    'body' => 'un nouveau participant ' . $form->get('nom')->getData(),
                ]
            );


  /////////////////////////Notifier Participant/////////////////////////////     
    $mailer = new PHPMailer();
    $mailer->isSMTP();
    $mailer->SMTPSecure = 'tls';
    $mailer->SMTPAutoTLS = false;
    $mailer->Host = 'smtp.gmail.com';
    $mailer->Port = 587;
    $mailer->SMTPAuth = true;
    $mailer->Username = 'rayan.lahmar@esprit.tn';
    $mailer->Password = '201JMT3276';
    $mailer->setFrom('E-Fit');
    $mailer->addAddress($participant->getEmail());
   
    
    $mailer->Subject = 'Participation';
    $mailer->Body = 'Participation chez ' .$evenement->getNom().' avec succes';
    
    if (!$mailer->send()) {
        // Gestion des erreurs d'envoi de l'e-mail
        $this->addFlash('danger', 'Une erreur est survenue lors de l\'envoi de l\'e-mail de notification.');
    } else {
        // Succès de l'envoi de l'e-mail
        $this->addFlash('success', 'L\'événement a été supprimé avec succès et un e-mail de notification a été envoyé.');
    }

*/


        return $this->redirectToRoute('afficher', ['id' => $participant->getId()]);
    }

    return $this->render('Participant/add3.html.twig', [
        'formParticipant' => $form->createView(),
    ]);
}

     
#[Route('/Participant/afficherjson1', name: 'json1')]

public function afficherparticipantjson(ManagerRegistry $mg,NormalizerInterface $normalizer): Response
{
    $repo=$mg->getRepository(Participant::class);
    $resultat = $repo ->FindAll();
    $ParticipantNormalises=$normalizer->normalize($resultat,'json',['groups'=>"Participant"]);
    $json=json_encode($ParticipantNormalises);
    return new Response ($json);

   
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

 


    /**
 * @Route("/participant/pdfP/{id}", name="pdfP")
 */
public function pdfP(ParticipantRepository $repository, $id): Response
{
    // Configure Dompdf according to your needs
    $pdfOptions = new Options();
    $pdfOptions->set('defaultFont', 'Arial');

    // Instantiate Dompdf with our options
    $dompdf = new Dompdf($pdfOptions);
    $participant = $repository->find($id);

    // Retrieve the HTML generated in our twig file
    $html = $this->renderView('Participant/pdf.html.twig', [
        'participant' => $participant = $this->getDoctrine()->getRepository(Participant::class)->find($id)
    ]);

    // Load HTML to Dompdf
    $dompdf->loadHtml($html);

    // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
    $dompdf->setPaper('A4', 'portrait');

    // Render the HTML as PDF
    $dompdf->render();

    // Output the generated PDF to Browser (force download)
    $dompdf->stream("mypdf.pdf", [
        "Attachment" => false
    ]);
    exit(0);
}
  

 #[Route('/searchParticipant', name: 'searchParticipant')]
public function searchParticipantx(Request $request, NormalizerInterface $Normalizer, ParticipantRepository $sr)
{
    $repository = $this->getDoctrine()->getRepository(Participant::class);
    $requestString = $request->get('searchValue');
    $Participants = $repository->findParticipantByNom($requestString);
    $jsonContent = $Normalizer->normalize($Participants, 'json', ['groups' => 'Participant']);
    $retour = json_encode($jsonContent);
    return new Response($retour);
}

//*****************************************MOBILE********************************************************* */






#[Route('/updatejson1/{id}', name: 'updatejson')]
public function updatejson(Request $req, $id, NormalizerInterface $Normalizer)
{
    $em = $this->getDoctrine()->getManager();
    $Participant = $em->getRepository(Participant::class)->find($id);
    
    $nom = $req->get('nom');
    $prenom = $req->get('prenom');
    $email = $req->get('email');
    $tel = $req->get('tel');
    $age = $req->get('age');

    // Set the updated values in the entity
    if ($nom) {
        $Participant->setNom($nom);
    }
    if ($prenom) {
        $Participant->setPrenom($prenom);
    }
    if ($email) {
        $Participant->setEmail($email);
    }
   
    if ($tel) {
        $Participant->setTel(intval($tel));
    }
    if ($age) {
        $Participant->setAge(intval($age));
    }
   

    $em->persist($Participant);
    $em->flush();

    $jsonContent = $Normalizer->normalize($Participant, 'json', ['groups' => 'Participants']);
    return new Response("Participant updated successfully" . json_encode($jsonContent));
}


#[Route('/deletejson1/{id}', name: 'deletejson')]
public function deletejson(Request $request, $id, NormalizerInterface $normalizer)
{
    $em = $this->getDoctrine()->getManager();
    $Participant = $em->getRepository(Participant::class)->find($id);

    if ($Participant !== null) {
        $em->remove($Participant);
        $em->flush();

        $jsonContent = $normalizer->normalize($Participant, 'json', ['groups' => 'Participants']);
        return new Response("Participant deleted successfully: " . json_encode($jsonContent));
    } else {
        return new Response("Participant not found.");
    }
}
}



