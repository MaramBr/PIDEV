<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Evenement;//controller
use Doctrine\Persistence\ManagerRegistry;//controller
use App\RepositoryEvenementRepository;//controller
use App\Form\EvenementType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Repository\EvenementRepository;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Evenement\CancellationEvenement;
use Symfony\Component\EvenementDispatcher\EvenementDispatcherInterface;
use Symfony\App\Controller\EventNotificationService;
use App\Service;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;



 
class EvenementController extends AbstractController
{     
    
    
    #[Route('/Evenement', name: 'app_Evenement')]
    public function index(): Response
    {
        return $this->render('Evenement/index.html.twig', [
            'controller_name' => 'EvenementController',
        ]);
    }

   
    #[Route('/Evenement/afficher', name: 'affichefront')]
    public function affiche1(ManagerRegistry $em): Response
    {
        $repo=$em->getRepository(Evenement::class);
        $result=$repo->findAll();
        return $this->render ('Evenement/affich.html.twig',['Evenement'=>$result]);
   
       
    }


    #[Route('/Evenement/afficherback', name: 'afficheback')]
    public function afficheback(ManagerRegistry $em): Response
    {
        $repo=$em->getRepository(Evenement::class);
        $result=$repo->findAll();
      
        return $this->render ('Evenement/back.html.twig',['Evenement'=>$result]);
   
       
    }
    




    #[Route('/Evenement/add', name: 'add_front')]
    public function add1(ManagerRegistry $doctrine,Request $request ,SluggerInterface $slugger): Response
    {
        $Evenement=new Evenement() ;
        $form=$this->createForm(EvenementType::class,$Evenement); //sna3na objet essmo form aamlena bih appel lel Evenementtype
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) //amaalna verification esq taadet willa le aadna prob fi code ou nn
       {
        $brochureFile = $form->get('image')->getData();

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
                        $this->getParameter('Evenement_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $Evenement->setImage($newFilename);
            }
        $em=$doctrine->getManager(); //appel lel manager
        $em->persist($Evenement); //elli tzid
        $em->flush(); //besh ysob fi base de donnee
        return $this->redirectToRoute('affichefront');
        
        }
        return $this->render('Evenement/add.html.twig', array("formEvenement"=>$form->createView()));
       // return $this->render('Evenement/add.html.twig', array("formEvenement"=>$form->createView));

    }

    #[Route('/Evenement/add1', name: 'add_back')]
    public function add2(ManagerRegistry $doctrine,Request $request ,SluggerInterface $slugger): Response
    {
        $Evenement=new Evenement() ;
        $form=$this->createForm(EvenementType::class,$Evenement); //sna3na objet essmo form aamlena bih appel lel Evenementtype
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) //amaalna verification esq taadet willa le aadna prob fi code ou nn
       {
        $brochureFile = $form->get('image')->getData();

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
                        $this->getParameter('Evenement_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $Evenement->setImage($newFilename);
            }
        $em=$doctrine->getManager(); //appel lel manager
        $em->persist($Evenement); //elli tzid
        $em->flush(); //besh ysob fi base de donnee
        return $this->redirectToRoute('afficheback');
        
        }
        return $this->render('Evenement/add1.html.twig', array("formEvenement"=>$form->createView()));
       // return $this->render('Evenement/add.html.twig', array("formEvenement"=>$form->createView));

    }
   

     #[Route('/Evenement/update/{id}', name: 'updateEvenement')]
    public function updateEvenement(Request $request, $id, EvenementRepository $repo,
    ManagerRegistry $mg
    ): Response
    {$Evenement=$repo->find($id);
        $form = $this->createForm(EvenementType::class, $Evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()  ) {
         $em=$mg->getManager();
         $em->persist($Evenement); 
         $em->flush();
            return $this->redirectToRoute('afficheback');
        }

        return $this->renderForm('Evenement/add.html.twig', [
            'formEvenement' => $form,
        ]);
    }

    

    #[Route('/Evenement/delete/{id}', name: 'deleteEvenement')]

    public function deleteEvenement($id, ManagerRegistry $doctrine,MailerInterface $mailer)
    {$c = $doctrine
        ->getRepository(Evenement::class)
        ->find($id);
        $em = $doctrine->getManager();
        $em->remove($c);
        $em->flush() ;



        $email = (new Email())
        ->from('emna.abbessi@esprit.tn')
        ->To('emna.abessi55@gmail.com')
        ->subject('Event Annuler')
        ->text(" l'evenement est canceled");
        
         try {
        $mailer->send($email);
        $this->addFlash('message','E-mail  de réinitialisation du mp envoyé :');
    } catch (TransportExceptionInterface $e) {
           

        
       
 
        return $this->redirectToRoute('afficheback');
    }

}
 
    
    
 #[Route('/searchEvenement', name: 'searchEvenement')]
  
 public function searchEvenementx(Request $request,NormalizerInterface $Normalizer,EvenementRepository $sr)
 {
$repository = $this->getDoctrine()->getRepository(Evenement::class);
$requestString=$request->get('searchValue');
$Evenements = $sr->findEvenementByNom($requestString);
$jsonContent = $Normalizer
->normalize($Evenements,'json',['groups'=>'Evenements']);
$retour=json_encode($jsonContent);
return new Response($retour);

}

public function sendEmail(MailerInterface $mailer): Response
    {   $to ='emna.abbessi@esprit.tn';
        $email = (new Email())
            ->from('emna.abbessi@esprit.tn')
            ->to('$to')
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Time for Symfony Mailer!')
            ->text('Sending emails is fun again!')
            ->html('<p>See Twig integration for better HTML integration!</p>');

        return $this->mailer->send($email);

      
    }
    
}


