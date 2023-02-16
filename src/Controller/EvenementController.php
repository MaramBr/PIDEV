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
    




    #[Route('/Evenement/add1', name: 'add_back')]
    public function add2(ManagerRegistry $doctrine,Request $request): Response
    {
        $Evenement=new Evenement() ;
        $form=$this->createForm(EvenementType::class,$Evenement); //sna3na objet essmo form aamlena bih appel lel Evenementtype
        $form->handleRequest($request);
       if( $form->isSubmitted())  //amaalna verification esq taadet willa le aadna prob fi code ou nn
       {
        $em=$doctrine->getManager(); //appel lel manager
        $em->persist($Evenement); //elli tzid
        $em->flush(); //besh ysob fi base de donnee
        return $this->redirectToRoute('afficheback');
        
        }
        return $this->render('Evenement/add1.html.twig', array("formEvenement"=>$form->createView()));
       // return $this->render('Evenement/add.html.twig', array("formEvenement"=>$form->createView));

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
   

     #[Route('/Evenement/update/{id}', name: 'updateEvenement')]
    public function updateEvenement(Request $request, $id, EvenementRepository $repo,
    ManagerRegistry $mg
    ): Response
    {$Evenement=$repo->find($id);
        $form = $this->createForm(EvenementType::class, $Evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted()  ) {
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

    public function deleteEvenement($id, ManagerRegistry $doctrine)
    {$c = $doctrine
        ->getRepository(Evenement::class)
        ->find($id);
        $em = $doctrine->getManager();
        $em->remove($c);
        $em->flush() ;
        return $this->redirectToRoute('afficheback');
    }
 
    
}
