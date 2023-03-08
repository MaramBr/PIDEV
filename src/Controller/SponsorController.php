<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Sponsor;//controller
use Doctrine\Persistence\ManagerRegistry;//controller
use App\Repository\SponsorRepository;//controller
use App\Form\SponsorType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
//use Symfony\Component\Validator\Constraints as Assert;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\PieChart;
 
class SponsorController extends AbstractController
{
    #[Route('/Sponsor', name: 'app_Sponsor')]
    public function index(): Response
    {
        return $this->render('Sponsor/index.html.twig', [
            'controller_name' => 'SponsorController',
        ]);
    }

   
    
    #[Route('/Sponsor/afficherback', name: 'appback')]
    public function afficheback(ManagerRegistry $em): Response
    {
        $repo=$em->getRepository(Sponsor::class);
        $result=$repo->findAll();
        return $this->render ('Sponsor/back.html.twig',['Sponsor'=>$result]);
   
       
    }




    #[Route('/Sponsor/add', name: 'add')]
    public function add(ManagerRegistry $doctrine,Request $request): Response
    {
        $Sponsor=new Sponsor() ;
        $form=$this->createForm(SponsorType::class,$Sponsor); //sna3na objet essmo form aamlena bih appel lel Sponsortype
        $form->handleRequest($request);
        if( $form->isSubmitted() && $form->isValid() )  //amaalna verification esq taadet willa le aadna prob fi code ou nn
       {
        $em=$doctrine->getManager(); //appel lel manager
        $em->persist($Sponsor); //elli tzid
        $em->flush(); //besh ysob fi base de donnee
        return $this->redirectToRoute('appback');
        
        }
        return $this->render('Sponsor/add.html.twig', array("formSponsor"=>$form->createView()));
       // return $this->render('Sponsor/add.html.twig', array("formSponsor"=>$form->createView));

    }

     
   

    #[Route('/Sponsor/update/{id}', name: 'update')]

    public function  updateSponsor (ManagerRegistry $doctrine,$id,  Request  $request) : Response
    { $Sponsor = $doctrine
        ->getRepository(Sponsor::class)
        ->find($id);
        $form = $this->createForm(SponsorType::class, $Sponsor);
        $form->add('update', SubmitType::class) ;
        $form->handleRequest($request);
        if ($form->isSubmitted())
        { $em = $doctrine->getManager();
            $em->flush();
            return $this->redirectToRoute('appback');
        }
        return $this->renderForm("Sponsor/add.html.twig",
            ["formSponsor"=>$form]) ;


    } 

    #[Route('/Sponsor/delete/{id}', name: 'delete')]

    public function delete($id, ManagerRegistry $doctrine)
    {$c = $doctrine
        ->getRepository(Sponsor::class)
        ->find($id);
        $em = $doctrine->getManager();
        $em->remove($c);
        $em->flush() ;
        return $this->redirectToRoute('appback');
    }

     #[Route('/searchSponsor', name: 'searchSponsor')]
public function searchSponsorx(Request $request, NormalizerInterface $Normalizer, SponsorRepository $sr)
{
    $repository = $this->getDoctrine()->getRepository(Sponsor::class);
    $requestString = $request->get('searchValue');
    $Sponsors = $repository->findSponsorByNom($requestString);
    $jsonContent = $Normalizer->normalize($Sponsors, 'json', ['groups' => 'Sponsor']);
    $retour = json_encode($jsonContent);
    return new Response($retour);
}

#[Route('/stat2' , name:'stat2')]
  
    public function stat( SponsorRepository $repository)
    {
        
        $newSponsorCount =$repository->findNewSponsor();
        $SponsorTrite = $repository->findSponsorsTritée();
        $SponsorNonTrite = $repository->findSponsorsNonTritée();
        $EmnaChart1 = new PieChart();
        $EmnaChart1->getData()->setArrayToDataTable(
            [['Task', 'Hours per Day'],
                ['Sponsor Financier',((int) $SponsorTrite)],
                ['Sponsor Autre',((int) $SponsorNonTrite)],
            ]
        );
        $EmnaChart1->getOptions()->setTitle("L'ETAT DES Sponsor D'AUJOURD'HUIT");
        $EmnaChart1->getOptions()->setHeight(400);
        $EmnaChart1->getOptions()->setIs3D(2);
        $EmnaChart1->getOptions()->setWidth(550);
        $EmnaChart1->getOptions()->getTitleTextStyle()->setBold(true);
        $EmnaChart1->getOptions()->getTitleTextStyle()->setColor('#009900');
        $EmnaChart1->getOptions()->getTitleTextStyle()->setItalic(true);
        $EmnaChart1->getOptions()->getTitleTextStyle()->setFontName('Arial');
        $EmnaChart1->getOptions()->getTitleTextStyle()->setFontSize(15);

        return $this->render('Sponsor/statistique.html.twig', array(
            'EmnaChart1' => $EmnaChart1 ,
          ));
    



    }

 
}
