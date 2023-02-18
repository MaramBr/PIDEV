<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Company;//controller
use Doctrine\Persistence\ManagerRegistry;//controller
use App\RepositoryCompanyRepository;//controller
use App\Form\CompanyType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Repository\CompanyRepository;

 
class CompanyController extends AbstractController
{
    #[Route('/Company', name: 'app_Company')]
    public function index(): Response
    {
        return $this->render('Company/index.html.twig', [
            'controller_name' => 'CompanyController',
        ]);
    }

   
    #[Route('/Company/afficherback2', name: 'appback2')]
    public function afficheback2(ManagerRegistry $em): Response
    {
        $repo=$em->getRepository(Company::class);
        $result=$repo->findAll();
        return $this->render ('Company/back.html.twig',['Company'=>$result]);
   
       
    }
    #[Route('/Company/add2', name: 'add2')]
    public function add2(ManagerRegistry $doctrine,Request $request): Response
    {
        $Company=new Company() ;
        $form=$this->createForm(CompanyType::class,$Company); //sna3na objet essmo form aamlena bih appel lel Companytype
        $form->handleRequest($request);
        if( $form->isSubmitted() && $form->isValid() )   //amaalna verification esq taadet willa le aadna prob fi code ou nn
       {
        $em=$doctrine->getManager(); //appel lel manager
        $em->persist($Company); //elli tzid
        $em->flush(); //besh ysob fi base de donnee
        return $this->redirectToRoute('appback2');
        }
        return $this->render('Company/add.html.twig', array("formCompany"=>$form->createView()));
       // return $this->render('Company/add.html.twig', array("formCompany"=>$form->createView));
    }
    #[Route('/Company/update2/{id}', name: 'update2')]

    public function  updateCompany2 (ManagerRegistry $doctrine,$id,  Request  $request) : Response
    { $Company = $doctrine
        ->getRepository(Company::class)
        ->find($id);
        $form = $this->createForm(CompanyType::class, $Company);
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid() )
        { $em = $doctrine->getManager();
            $em->flush();
            return $this->redirectToRoute('appback2');
        }
        return $this->renderForm("Company/update.html.twig",
            ["Company"=>$form]) ;
    } 
    #[Route('/Company/delete2/{id}', name: 'delete2')]
    public function delete2($id, ManagerRegistry $doctrine)
    {$c = $doctrine
        ->getRepository(Company::class)
        ->find($id);
        $em = $doctrine->getManager();
        $em->remove($c);
        $em->flush() ;
        return $this->redirectToRoute('appback2');
    }
 
}
