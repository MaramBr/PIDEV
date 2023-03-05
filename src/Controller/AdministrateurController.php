<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Repository\CommandeRepository;
use App\Repository\UserRepository;
use App\Repository\ProduitRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Captcha\Bundle\CaptchaBundle\Validator\Constraints\ValidCaptcha;
use Gregwar\CaptchaBundle\Type\CaptchaType;
use Gregwar\CaptchaBundle\CaptchaBuilderInterface;
//use App\Form\CaptchaType;
use Dompdf\Dompdf;
use Dompdf\Options;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\PieChart;

class AdministrateurController extends AbstractController
{
    #[Route('/administrateur', name: 'app_administrateur')]
    public function index(): Response
    {
        return $this->render('administrateur/index.html.twig', [
            'controller_name' => 'AdministrateurController',
        ]);
    }

     /**
     * @Route("/administrateur/commande", name="listcommande")
     */
    public function listCommande(CommandeRepository $repository): Response
    {
        $data = $repository->findBy(['utilisateur'=>1]);
        return $this->render('administrateur/commande.html.twig', [
            'data' => $data,
        ]);
    }
     /**
     * @Route("/administrateur/updateCommande{idP}", name="updateCommande")
     */
    public function updateCommande($idP,CommandeRepository $repository , Request $request): Response
    {
        $comm = $repository->find($idP);
        $form = $this->createFormBuilder($comm)
            ->add('status',ChoiceType::class,[
                'choices'  => [
                    'En Attente' => 'En attente',
                    'Annulée' => 'Annulée',
                    'Confirmé' => 'Confirmée',
                    'En cours de preparation' => 'En cours de preparation',
                    'Livraison en cours' => 'Livraison en cours',
                    'Livrée' => 'Livrée',
                ]])
                ->add('captcha', CaptchaType::class,['attr' => ['class' => "form-control"],])
                
            ->add('Confirmer',SubmitType::class)
            ->getForm();
        $form->handleRequest($request);
        if($form->isSubmitted()&& $form->isValid()){
            $em=$this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash('updateStatus', 'Status du Commande modifier avec succès');
            return $this->redirectToRoute('listcommande');
        }
        return $this->render('administrateur/updateCommande.html.twig', [
            'formU' => $form->createView(),
        ]);
    }
          /**
     * @Route ("/commandeDelete2/{id}", name="commandeDelete2")
     */
    public function delete2(CommandeRepository $repository , $id): Response
    {
        $comm =$repository->find($id) ;
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($comm);
        $manager->flush();
        //return new Response('suppression avec succes');
        return $this->redirectToRoute('listcommande');
    }
    #[Route('/Dashboard', name: 'Dashboard')]
    public function dashboard(): Response
    {
        return $this->render('administrateur/dashboard.html.twig', [
            'controller_name' => 'AdministrateurController',
        ]);
    }


/**
 * @Route("/administrateur/commande/pdfC/{id}", name="pdfC")
 */
public function pdfC(CommandeRepository $repository, $id): Response
{
     // Configure Dompdf according to your needs
     $pdfOptions = new Options();
     $pdfOptions->set('defaultFont', 'Arial');

     // Instantiate Dompdf with our options
     $dompdf = new Dompdf($pdfOptions);
     $data = $repository->findBy(['utilisateur'=>1]);
    
   

    // Retrieve the HTML generated in our twig file
    $html = $this->renderView('administrateur/pdfC.html.twig', [
        'data' => $data =$this->getDoctrine()->getRepository(Commande::class)->findBy(['utilisateur'=>1])
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



#[Route('/searchcommande', name: 'searchcommande')]
public function searchcommandex(Request $request, NormalizerInterface $Normalizer, CommandeRepository $sr)
{
    $repository = $this->getDoctrine()->getRepository(Commande::class);
    $requestString = $request->get('searchValue');
    $commandes = $repository->findcommandeByNom($requestString);
    $jsonContent = $Normalizer->normalize($commandes, 'json', ['groups' => 'commande']);
    $retour = json_encode($jsonContent);
    return new Response($retour);
}





/**
     * @Route("/order_By_montant", name="order_By_montant" ,methods={"GET"})
     */
    public function order_By_montant(Request $request,CommandeRepository $CommandeRepository): Response
    {
//list of students order By Nom
        $Commande = $CommandeRepository->orderByMontant();

        return $this->render('administrateur/commande.html.twig', [
            'data' => $Commande,
        ]);

        //trie selon Nom

    }
    /**
     * @Route("/order_By_date", name="order_By_date" ,methods={"GET"})
     */
    public function order_By_date(Request $request,CommandeRepository $CommandeRepository): Response
    {
//list of students order By Nom
        $Commande = $CommandeRepository->orderByDate();

        return $this->render('administrateur/commande.html.twig', [
            'data' => $Commande,
        ]);

        //trie selon Nom

    }




    
    /**
     * @Route("/stat" , name="stat")
     */
    public function stat( ProduitRepository $produitRepository,CommandeRepository $repository ,UserRepository $repository1)
    {
        //**************************** Iheb ***************************
        $newCommandeCount =$repository->findNewCommande();
        $commandeTrite = $repository->findCommandesTritée();
        $commandeNonTrite = $repository->findCommandesNonTritée();
        $IhebChart1 = new PieChart();
        $IhebChart1->getData()->setArrayToDataTable(
            [['Task', 'Hours per Day'],
                ['Commande Traitée',((int) $commandeTrite)],
                ['Commande non Traitée',((int) $commandeNonTrite)],
            ]
        );
        $IhebChart1->getOptions()->setTitle("L'ETAT DES COMMANDES D'AUJOURD'HUIT");
        $IhebChart1->getOptions()->setHeight(400);
        $IhebChart1->getOptions()->setIs3D(2);
        $IhebChart1->getOptions()->setWidth(550);
        $IhebChart1->getOptions()->getTitleTextStyle()->setBold(true);
        $IhebChart1->getOptions()->getTitleTextStyle()->setColor('#009900');
        $IhebChart1->getOptions()->getTitleTextStyle()->setItalic(true);
        $IhebChart1->getOptions()->getTitleTextStyle()->setFontName('Arial');
        $IhebChart1->getOptions()->getTitleTextStyle()->setFontSize(15);

        return $this->render('administrateur/dashboard.html.twig', array(
            'IhebChart1' => $IhebChart1 ,
          ));
    



    }







}
