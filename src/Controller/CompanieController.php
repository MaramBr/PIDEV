<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Companie;//controller
use Doctrine\Persistence\ManagerRegistry;//controller
use App\RepositoryCompanieRepository;//controller
use App\Form\CompanieType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Repository\CompanieRepository;

 
class CompanieController extends AbstractController
{
    #[Route('/Companie', name: 'app_Companie')]
    public function index(): Response
    {
        return $this->render('Companie/index.html.twig', [
            'controller_name' => 'CompanieController',
        ]);
    }

   
    #[Route('/Companie/afficherback2', name: 'appback2')]
    public function afficheback2(ManagerRegistry $em): Response
    {
        $repo=$em->getRepository(Companie::class);
        $result=$repo->findAll();
        return $this->render ('Companie/back.html.twig',['Companie'=>$result]);
   
       
    }
    #[Route('/Companie/add2', name: 'add2')]
    public function add2(ManagerRegistry $doctrine,Request $request): Response
    {
        $Companie=new Companie() ;
        $form=$this->createForm(CompanieType::class,$Companie); //sna3na objet essmo form aamlena bih appel lel Companietype
        $form->handleRequest($request);
        if( $form->isSubmitted() && $form->isValid() )   //amaalna verification esq taadet willa le aadna prob fi code ou nn
       {
        $em=$doctrine->getManager(); //appel lel manager
        $em->persist($Companie); //elli tzid
        $em->flush(); //besh ysob fi base de donnee
        return $this->redirectToRoute('appback2');
        }
        return $this->render('Companie/add.html.twig', array("formCompanie"=>$form->createView()));
       // return $this->render('Companie/add.html.twig', array("formCompanie"=>$form->createView));
    }
    #[Route('/Companie/update2/{id}', name: 'update2')]

    public function  updateCompanie2 (ManagerRegistry $doctrine,$id,  Request  $request) : Response
    { $Companie = $doctrine
        ->getRepository(Companie::class)
        ->find($id);
        $form = $this->createForm(CompanieType::class, $Companie);
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid() )
        { $em = $doctrine->getManager();
            $em->flush();
            return $this->redirectToRoute('appback2');
        }
        return $this->renderForm("Companie/update.html.twig",
            ["Companie"=>$form]) ;
    } 
    #[Route('/Companie/delete2/{id}', name: 'delete2')]
    public function delete2($id, ManagerRegistry $doctrine)
    {$c = $doctrine
        ->getRepository(Companie::class)
        ->find($id);
        $em = $doctrine->getManager();
        $em->remove($c);
        $em->flush() ;
        return $this->redirectToRoute('appback2');
    }
 
}