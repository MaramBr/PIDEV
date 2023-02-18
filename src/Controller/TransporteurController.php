<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Transporteur;//controller
use Doctrine\Persistence\ManagerRegistry;//controller
use App\Repository\TransporteurRepository;//controller
use App\Form\TransporteurType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\String\Slugger\SluggerInterface;

 
class TransporteurController extends AbstractController
{
    #[Route('/Transporteur', name: 'app_Transporteur')]
    public function index(): Response
    {
        return $this->render('Transporteur/index.html.twig', [
            'controller_name' => 'TransporteurController',
        ]);
    }

   
    #[Route('/Transporteur/afficher', name: 'app')]
    public function affiche1(ManagerRegistry $em): Response
    {
        $repo=$em->getRepository(Transporteur::class);
        $result=$repo->findAll();
        return $this->render ('Transporteur/affich.html.twig',['Transporteur'=>$result]);
   
       
    }
    #[Route('/Transporteur/afficherback', name: 'appback')]
    public function afficheback(ManagerRegistry $em): Response
    {
        $repo=$em->getRepository(Transporteur::class);
        $result=$repo->findAll();
        return $this->render ('Transporteur/back.html.twig',['Transporteur'=>$result]);
   
       
    }




    #[Route('/Transporteur/add', name: 'add')]
    public function add(ManagerRegistry $doctrine,Request $request, SluggerInterface $slugger): Response
    {
        $Transporteur=new Transporteur() ;
        $form=$this->createForm(TransporteurType::class,$Transporteur); //sna3na objet essmo form aamlena bih appel lel Transporteurtype
        $form->handleRequest($request);
        if( $form->isSubmitted() && $form->isValid() )   //amaalna verification esq taadet willa le aadna prob fi code ou nn
       {
        $image = $form->get('image')->getData();

        // this condition is needed because the 'brochure' field is not required
        // so the PDF file must be processed only when a file is uploaded
        if ($image) {
            $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
            // this is needed to safely include the file name as part of the URL
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$image->guessExtension();

            // Move the file to the directory where brochures are stored
            try {
                $image->move(
                    $this->getParameter('produit_directory'),
                    $newFilename
                );
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
            }

            // updates the 'brochureFilename' property to store the PDF file name
            // instead of its contents
            $Transporteur->setImage($newFilename);
        }
        $em=$doctrine->getManager(); //appel lel manager
        $em->persist($Transporteur); //elli tzid
        $em->flush(); //besh ysob fi base de donnee
        return $this->redirectToRoute('appback');
        }
        return $this->render('Transporteur/add.html.twig', array("formTransporteur"=>$form->createView()));
       // return $this->render('Transporteur/add.html.twig', array("formTransporteur"=>$form->createView));

    }
    #[Route('/Transporteur/add1', name: 'add1')]
    public function add1(ManagerRegistry $doctrine,Request $request, SluggerInterface $slugger): Response
    {
        $Transporteur=new Transporteur() ;
        $form=$this->createForm(TransporteurType::class,$Transporteur); //sna3na objet essmo form aamlena bih appel lel Transporteurtype
        $form->handleRequest($request);
        if( $form->isSubmitted() && $form->isValid() )   //amaalna verification esq taadet willa le aadna prob fi code ou nn
       {
        $image = $form->get('image')->getData();

        // this condition is needed because the 'brochure' field is not required
        // so the PDF file must be processed only when a file is uploaded
        if ($image) {
            $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
            // this is needed to safely include the file name as part of the URL
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$image->guessExtension();

            // Move the file to the directory where brochures are stored
            try {
                $image->move(
                    $this->getParameter('produit_directory'),
                    $newFilename
                );
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
            }

            // updates the 'brochureFilename' property to store the PDF file name
            // instead of its contents
            $Transporteur->setImage($newFilename);
        }
        $em=$doctrine->getManager(); //appel lel manager
        $em->persist($Transporteur); //elli tzid
        $em->flush(); //besh ysob fi base de donnee
        return $this->redirectToRoute('app');
        }
        return $this->render('Transporteur/add1.html.twig', array("formTransporteur"=>$form->createView()));
       // return $this->render('Transporteur/add.html.twig', array("formTransporteur"=>$form->createView));

    }

    #[Route('/Transporteur/update/{id}', name: 'update')]

    public function  updateTransporteur (ManagerRegistry $doctrine,$id,  Request  $request) : Response
    { $Transporteur = $doctrine
        ->getRepository(Transporteur::class)
        ->find($id);
        $form = $this->createForm(TransporteurType::class, $Transporteur);
        $form->add('update', SubmitType::class) ;
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid() )
        { $em = $doctrine->getManager();
            $em->flush();
            return $this->redirectToRoute('appback');
        }
        return $this->renderForm("Transporteur/update.html.twig",
            ["Transporteur"=>$form]) ;


    } 

    #[Route('/Transporteur/delete/{id}', name: 'delete')]

    public function delete($id, ManagerRegistry $doctrine)
    {$c = $doctrine
        ->getRepository(Transporteur::class)
        ->find($id);
        $em = $doctrine->getManager();
        $em->remove($c);
        $em->flush() ;
        return $this->redirectToRoute('appback');
    }
 
}
