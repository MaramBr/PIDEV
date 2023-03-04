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

use Knp\Snappy\Pdf;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Dompdf\Dompdf;
use Dompdf\Options;
use PHPMailer\PHPMailer\PHPMailer;


use Knp\Component\Pager\PaginatorInterface;

 
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
    public function affiche1(ManagerRegistry $em,PaginatorInterface $paginator,Request $request): Response
    {
        $repo=$em->getRepository(Evenement::class);
        $result=$repo->findAll();
        $Evenement= $paginator->paginate(
            $result,
            $request->query->getInt('page',1),//num page
            3
        );
        return $this->render ('Evenement/affich.html.twig',['result'=>$Evenement]);
   
       
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
public function deleteEvenement($id, ManagerRegistry $doctrine, MailerInterface $mailer)
{   
    
    $evenement = $doctrine->getRepository(Evenement::class)->find($id);
    
    if (!$evenement) {
        throw $this->createNotFoundException('Evenement introuvable');
    }
    
    // Supprimer l'événement
    $entityManager = $doctrine->getManager();
    $entityManager->remove($evenement);
    $entityManager->flush();
    
    // Envoyer un e-mail de notification
    $participants = $evenement->getParticipants();
    $mailer = new PHPMailer();
    $mailer->isSMTP();
    $mailer->SMTPSecure = 'tls';
    $mailer->SMTPAutoTLS = false;
    $mailer->Host = 'smtp.gmail.com';
    $mailer->Port = 587;
    $mailer->SMTPAuth = true;
    $mailer->Username = 'emna.abbessi@esprit.tn';
    $mailer->Password = '12715163';
    $mailer->setFrom('E-Fit');
    
    foreach ($participants as $participant) {
        $mailer->addAddress($participant->getEmail());
    }
    
    $mailer->Subject = 'Evenement Annulé';
    $mailer->Body = 'L\'événement ' .$evenement->getNom().' a été Annulé.';
    
    if (!$mailer->send()) {
        // Gestion des erreurs d'envoi de l'e-mail
        $this->addFlash('danger', 'Une erreur est survenue lors de l\'envoi de l\'e-mail de notification.');
    } else {
        // Succès de l'envoi de l'e-mail
        $this->addFlash('success', 'L\'événement a été supprimé avec succès et un e-mail de notification a été envoyé.');
    }
    
    return $this->redirectToRoute('afficheback');
}

    
 #[Route('/searchEvenement', name: 'searchEvenement')]
public function searchEvenementx(Request $request, NormalizerInterface $Normalizer, EvenementRepository $sr)
{
    $repository = $this->getDoctrine()->getRepository(Evenement::class);
    $requestString = $request->get('searchValue');
    $Evenements = $repository->findEvenementByNom($requestString);
    $jsonContent = $Normalizer->normalize($Evenements, 'json', ['groups' => 'Evenement']);
    $retour = json_encode($jsonContent);
    return new Response($retour);
}


/**
     * @Route("/order_By_Nom", name="order_By_Nom" ,methods={"GET"})
     */
    public function order_By_Nom(Request $request,EvenementRepository $EvenementRepository): Response
    {
//list of students order By Nom
        $Evenement = $EvenementRepository->orderByNom();

        return $this->render('Evenement/back.html.twig', [
            'Evenement' => $Evenement,
        ]);

        //trie selon Nom

    }

    /**
     * @Route("/order_By_type", name="order_By_type" ,methods={"GET"})
     */
    public function order_By_type(Request $request,EvenementRepository $EvenementRepository): Response
    {
//list of students order By type
        $Evenement = $EvenementRepository->orderBytype();

        return $this->render('Evenement/back.html.twig', [
            'Evenement' => $Evenement,
        ]);

        //trie selon type

    }
    
    


}

    



