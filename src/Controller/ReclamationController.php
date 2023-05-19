<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


use App\Entity\Reclamation;
use App\Form\ReclamationType;
use App\Form\UpdateReclamationType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\ReclamationRepository;
use Doctrine\Common\Collections\Expr\Value;
use Dompdf\Dompdf;
use Dompdf\Options;
use Twilio\Rest\Client;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\PieChart;


class ReclamationController extends AbstractController
{
    #[Route('/reclamation1', name: 'app_reclamation')]
    public function index(): Response
    {
        return $this->render('reclamation/index.html.twig', [
            'controller_name' => 'ReclamationController',
        ]);
    }

//////////////////////////////////////////ajout////////////////
     
     #[Route('/addreclamation', name: 'addrecl')]
     public function addrec(ManagerRegistry $doctrine, Request $request): Response
     {
        $utilisateur = $this->getUser();
        // dd($utilisateur);
        if(!$utilisateur)
    {
    return $this->redirectToRoute('app_login');
    
    }
    else{
         $reclamation = new Reclamation();
         $reclamation->setDateR(new \DateTime('now'));
         $user=$this->getUser();
        $reclamation->setUser($user);
         $form = $this->createForm(ReclamationType::class, $reclamation);
         $form->handleRequest($request);
     
         if ($form->isSubmitted() && $form->isValid()) {
             $em = $doctrine->getManager();
             $em->persist($reclamation);
             $em->flush();
     /*
             $accountSid = 'AC099883ea0f07c67cd19e55b497fceb12';
             $authToken = '0dd561ef29a8f933acf0a98205b376c0';
             $client = new Client($accountSid, $authToken);
     
             $message = $client->messages->create(
                 '+21692524435', // replace with admin's phone number
                 [
                     'from' => '+12766336884', // replace with your Twilio phone number
                     'body' => 'New réclamation par ' . $user,
                 ]
             );
     */
             return $this->redirectToRoute('app_affrecfront');
         }
     
         return $this->renderForm('reclamation/index.html.twig', ['form2' => $form]);
        }
     }
 //////////////////////////affiche fil back ////////////////////////
     #[Route('/affichreclamation1', name: 'app_affrec')]
     public function liste1 (ManagerRegistry $mg,PaginatorInterface $paginator ,Request $request): Response
     {
         $repo=$mg->getRepository(Reclamation::class);
         $resultat = $repo ->FindAll();
      $data= $paginator->paginate(
        $resultat,
        $request->query->getInt('page',1),//num page
        5
    );
         return $this->render('reclamation/afficherback.html.twig', [
             'rec' => $data,
         ]);
     } 
     #[Route('/search1', name: 'search1')]
public function searchEvenementx(Request $request, NormalizerInterface $Normalizer,UserRepository $sr)
{
    $repository = $this->getDoctrine()->getRepository(User::class);
    $requestString = $request->get('searchValue');
    $Evenements = $repository->findEvenementByNom($requestString);
    $jsonContent = $Normalizer->normalize($Evenements, 'json', ['groups' => 'User']);
    $retour = json_encode($jsonContent);
    return new Response($retour);
}


/**
     * @Route("/stat1" , name="stat1")
     */
    public function stat(ReclamationRepository $repository )
    {
        //**************************** Iheb ***************************
        $newCommandeCount =$repository->findNewCommande();
        $commandeTrite = $repository->findCommandesTritée();
        $commandeNonTrite = $repository->findCommandesNonTritée();
        $IhebChart1 = new PieChart();
        $IhebChart1->getData()->setArrayToDataTable(
            [['Task', 'Hours per Day'],
                ['Reclamation Confirmées',((int) $commandeTrite)],
                ['Reclamation En attente',((int) $commandeNonTrite)],
            ]
        );
        $IhebChart1->getOptions()->setTitle("L'ETAT DES Reclamations D'AUJOURD'HUIT");
        $IhebChart1->getOptions()->setHeight(400);
        $IhebChart1->getOptions()->setIs3D(2);
        $IhebChart1->getOptions()->setWidth(550);
        $IhebChart1->getOptions()->getTitleTextStyle()->setBold(true);
        $IhebChart1->getOptions()->getTitleTextStyle()->setColor('#009900');
        $IhebChart1->getOptions()->getTitleTextStyle()->setItalic(true);
        $IhebChart1->getOptions()->getTitleTextStyle()->setFontName('Arial');
        $IhebChart1->getOptions()->getTitleTextStyle()->setFontSize(15);

        return $this->render('reclamation/stat1.html.twig', array(
            'IhebChart1' => $IhebChart1 ,
          ));
    



    }
  //////////////////////////l'affiche fil front ////////////////////////
  #[Route('/affichreclamation2', name: 'app_affrecfront')]
  public function liste2 (ManagerRegistry $mg,PaginatorInterface $paginator ,Request $request): Response
  {       $utilisateur = $this->getUser();
    // dd($utilisateur);
    if(!$utilisateur)
{
return $this->redirectToRoute('app_login');

}
else{
     $repo=$mg->getRepository(Reclamation::class);
      $resultat = $repo ->FindAll();
      $data= $paginator->paginate(
        $resultat,
        $request->query->getInt('page',1),//num page
        5
    );
      return $this->render('reclamation/afficherfront.html.twig', [
          'recl' => $data,
      ]);
    }
  } 
 
 //////////////////////////modifier fil front ////////////////////////
     #[Route('/updater/{id}', name: 'app_updater')]
     public function updateType(ManagerRegistry $doctrine,ReclamationRepository $ReclamationRepository,$id,Request $request): Response
     {
    $reclamation=$ReclamationRepository->find($id);
    $form=$this->CreateForm(ReclamationType :: class,$reclamation);
    $form->handleRequest($request);
    if($form->isSubmitted() )
    {
    $em=$doctrine->getManager();
    $em->persist($reclamation);
    $em->flush();
  return $this->redirectToRoute('app_affrecfront');
    }
    return $this->renderForm('reclamation/index.html.twig',array("form2"=>$form));
 
     }
 //////////////////////////modifier fil back ////////////////////////
     #[Route('/updaterb/{id}', name: 'app_updaterb')]
     public function updateTypeback(ManagerRegistry $doctrine,ReclamationRepository $ReclamationRepository,$id,Request $request): Response
     {
    $reclamation=$ReclamationRepository->find($id);
    $form=$this->CreateForm(UpdateReclamationType:: class,$reclamation);
    $form->handleRequest($request);
    if($form->isSubmitted() )
    {
    $em=$doctrine->getManager();
    $em->persist($reclamation);
    $em->flush();
  return $this->redirectToRoute('app_affrec');
    }
    return $this->renderForm('reclamation/updateback.html.twig',array("form2"=>$form));
 
     }
 
 //////////////////////////supprimer fil bac ////////////////////////
 #[Route('/removerec/{id}', name: 'app_removerecback')]
 public function remove (ManagerRegistry $mg ,ReclamationRepository $X , $id): Response
 {    
 
 $reclamation= $X->find($id);
 $em=$mg->getManager();
 $em->remove($reclamation);
 $em->flush();
 return $this->redirectToRoute('app_affrec');
 }       
 
 //////////////////////////supprimer fil front ////////////////////////
 #[Route('/removerecfront/{id}', name: 'app_removerec')]
 public function removefront (ManagerRegistry $mg ,ReclamationRepository $X , $id): Response
 {    
 
 $reclamation= $X->find($id);
 $em=$mg->getManager();
 $em->remove($reclamation);
 $em->flush();
 return $this->redirectToRoute('app_affrecfront');
 }  
 /*/////////////////////////////////////////////////////////////////////////////////////
 #[Route('/reclamations/ajax-search', name: 'reclamation_ajax_search')]
 public function ajaxSearchAction(Request $request)
     {
         // récupérer la valeur de recherche envoyée depuis la requête AJAX
         $searchTerm = $request->get('search');
 
         // effectuer la recherche en utilisant le terme de recherche
         $reclamations = $this->getDoctrine()->getRepository(Reclamation::class)->findByTerm($searchTerm);
 
         // retourner les résultats de la recherche sous forme de réponse JSON
         return new JsonResponse($reclamations);
     }*/
 //Exporter pdf (composer require dompdf/dompdf)
     /**
      * @Route("/pdf", name="PDF_Reclamation", methods={"GET"})
      */
     public function pdf(ReclamationRepository $ReclamationRepository)
     {
         // Configure Dompdf according to your needs
         $pdfOptions = new Options();
         $pdfOptions->set('defaultFont', 'Arial');
 
         // Instantiate Dompdf with our options
         $dompdf = new Dompdf($pdfOptions);
         // Retrieve the HTML generated in our twig file
         $html = $this->renderView('reclamation/pdf.html.twig', [
             'reclamations' => $ReclamationRepository->findAll(),
         ]);
 
         // Load HTML to Dompdf
         $dompdf->loadHtml($html);
         // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
         $dompdf->setPaper('A4', 'portrait');
 
         // Render the HTML as PDF
         $dompdf->render();
         // Output the generated PDF to Browser (inline view)
         $dompdf->stream("ListeDesreclmations.pdf", [
             "reclamations" => true
         ]);
     }


}
