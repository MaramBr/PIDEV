<?php

namespace App\Controller;
use App\Entity\Category;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Produit;//controller
use Doctrine\Persistence\ManagerRegistry;//controller
use App\RepositoryProduitRepository;//controller
use App\Form\ProduitType;
use App\Form\SearchProduitType;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Psr\Log\LoggerInterface;
use App\Entity\Comments;
use App\Form\CommentsType;
use DateTime;
use Knp\Component\Pager\PaginatorInterface;

 
class ProduitController extends AbstractController
{
    #[Route('/Produit', name: 'app_Produit')]
    public function index(): Response
    {
        return $this->render('Produit/index.html.twig', [
            'controller_name' => 'ProduitController',
        ]);
    }

   
    #[Route('/Produit/afficher', name: 'app')]
    
    
        public function affiche1(ManagerRegistry $em,PaginatorInterface $paginator,Request $request): Response
        {
            $repo=$em->getRepository(Produit::class);
            $data=$repo->findAll();
            $Produit= $paginator->paginate(
                $data,
                $request->query->getInt('page',1),//num page
                3
            );
            return $this->render ('produit/affich.html.twig',['data'=>$Produit]);
       
           
        }

       
    
    #[Route('/afficherjson', name: 'json')]

    public function affichercoachjson(ManagerRegistry $mg,NormalizerInterface $normalizer): Response
    {
        $repo=$mg->getRepository(Produit::class);
        $resultat = $repo ->FindAll();
        $coachingNormalises=$normalizer->normalize($resultat,'json',['groups'=>"Produit"]);
        $json=json_encode($coachingNormalises);
        return new Response ($json);
    }







    #[Route('/detaille/{id}', name: 'detaille')]
    public function detaille($id,ManagerRegistry $mg, Produit $Produit, LoggerInterface $logger, Request $request): Response
    {
        $repo=$mg->getRepository(Produit::class);
        $resultat = $repo ->find($id);
        $logger->info("The array is: " . json_encode($resultat));
         // Partie commentaires
        // On crée le commentaire "vierge"
        $comment = new Comments;

        // On génère le formulaire
        $commentForm = $this->createForm(CommentsType::class, $comment);

        $commentForm->handleRequest($request);
 // Traitement du formulaire
 if($commentForm->isSubmitted() && $commentForm->isValid()){
    $comment->setCreatedAt(new DateTime());
    $comment->setProduits($Produit);

     // On récupère le contenu du champ parentid
     $parentid = $commentForm->get("parentid")->getData();
     //$parentid = $commentForm->getParent().id->getData();
         // On va chercher le commentaire correspondant
         $em = $this->getDoctrine()->getManager();
         if($parentid != null){
            $parent = $em->getRepository(Comments::class)->find($parentid);
        }

        // On définit le parent
        $comment->setParent($parent ?? null );
         $em->persist($comment);
         $em->flush();
         $this->addFlash('message', 'Votre commentaire a été bien envoyé');
            return $this->redirectToRoute('detaille', ['id' => $id]);


 }
 
        return $this->render('produit/readmore.html.twig', [
            'Produit' => $resultat,
            'commentForm' => $commentForm->createView()
        ]);

           




        
    }

    #[Route('/Produit/afficherback', name: 'appback')]
    public function afficheback(ManagerRegistry $em): Response
    {
        $repo=$em->getRepository(Produit::class);
        $result=$repo->findAll();
        return $this->render ('Produit/back.html.twig',['Produit'=>$result]);
   
       
    }




    #[Route('/Produit/add', name: 'add')]
    public function add(ManagerRegistry $doctrine,Request $request, SluggerInterface $slugger): Response
    {
        $Produit=new Produit() ;
        $form=$this->createForm(ProduitType::class,$Produit); //sna3na objet essmo form aamlena bih appel lel Produittype
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
            $Produit->setImage($newFilename);
        }
        $em=$doctrine->getManager(); //appel lel manager
        $em->persist($Produit); //elli tzid
        $em->flush(); //besh ysob fi base de donnee
        return $this->redirectToRoute('appback');
        }
        return $this->render('Produit/add.html.twig', array("formProduit"=>$form->createView()));
       // return $this->render('Produit/add.html.twig', array("formProduit"=>$form->createView));

    }
    #[Route('/Produit/add1', name: 'add1')]
    public function add1(ManagerRegistry $doctrine,Request $request, SluggerInterface $slugger): Response
    {
        $Produit=new Produit() ;
        $form=$this->createForm(ProduitType::class,$Produit); //sna3na objet essmo form aamlena bih appel lel Produittype
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
            $Produit->setImage($newFilename);
        }
        $em=$doctrine->getManager(); //appel lel manager
        $em->persist($Produit); //elli tzid
        $em->flush(); //besh ysob fi base de donnee
        return $this->redirectToRoute('app');
        }
        return $this->render('Produit/add1.html.twig', array("formProduit"=>$form->createView()));
       // return $this->render('Produit/add.html.twig', array("formProduit"=>$form->createView));

    }

    #[Route('/Produit/update/{id}', name: 'update')]

    public function  updateProduit (ManagerRegistry $doctrine,$id,  Request  $request) : Response
    { $Produit = $doctrine
        ->getRepository(Produit::class)
        ->find($id);
        $form = $this->createForm(ProduitType::class, $Produit);
        $form->add('update', SubmitType::class) ;
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid() )
        { $em = $doctrine->getManager();
            $em->flush();
            return $this->redirectToRoute('appback');
        }
        return $this->renderForm("Produit/update.html.twig",
            ["Produit"=>$form]) ;


    } 

    #[Route('/Produit/delete/{id}', name: 'delete')]

    public function delete($id, ManagerRegistry $doctrine)
    {$c = $doctrine
        ->getRepository(Produit::class)
        ->find($id);
        $em = $doctrine->getManager();
        $em->remove($c);
        $em->flush() ;
        return $this->redirectToRoute('appback');
    }
    //////
    

    //////
#[Route('/category/{id}', name: 'category_articles')]
    public function articles(Category $category): Response
    {
        $articles = $category->getProduits();
        return $this->render('category/listeP.html.twig', [
            'category' => $category,
            'articles' => $articles,
        ]);
    }


    #[Route('/listVoyageWithSearchPrix', name: 'listVoyageWithSearchPrix')]

    public function listProduitWithSearchPrix(Request $request, ProduitRepository $ProduitRepository,PaginatorInterface $paginator)
    {
        //All of Student
        $Produit= $ProduitRepository->findAll();
        $data= $paginator->paginate(
            $Produit,
            $request->query->getInt('page',1),//num page
            1
        );
        
        //search
        $sform = $this->createForm(SearchProduitType::class);
        $sform->add("Recherche",SubmitType::class);
        $sform->handleRequest($request);
        if ($sform->isSubmitted()) {
            $Prix_Produit = $sform->get('prix')->getData();
            $resulta = $ProduitRepository->searchprix($Prix_Produit);
            $data= $paginator->paginate(
                $resulta,
                $request->query->getInt('page',1),//num page
                1
            );
            return $this->render('Produit/search.html.twig', array(
                "data" => $data,
                "search" => $sform->createView()));
        }
        return $this->render('Produit/search.html.twig', array(
            "data" => $data,
            "search" => $sform->createView()));
    }


   /* #[Route('/filtreP', name: 'app_filtre')]
      public function filtre(ProduitRepository $productRepository ,Request $request, ManagerRegistry $em,PaginatorInterface $paginator)
    {
        $repo=$em->getRepository(Produit::class);
        
        $data= $paginator->paginate(
            $resulta,
            $request->query->getInt('page',1),//num page
            1
        );




        $minPrice = $request->query->get('min_price');
        $maxPrice = $request->query->get('max_price');

        $Produit = $productRepository->findByPriceRange($minPrice, $maxPrice);

        return $this->render('Produit/affich.html.twig', [
            'Produit' => $Produit,'data'=>$data
           
        ]);
    }*/

}
