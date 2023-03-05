<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\User;
use App\Entity\Commande;
use App\Entity\CommandeProduit;
use App\Entity\Panier;
use App\Entity\Produit;
use App\Repository\CommandeProduitRepository;
use Symfony\Component\String\ByteString;
use App\Repository\UserRepository;
use App\Repository\CommandeRepository;
use App\Repository\PanierRepository;
use App\Repository\ProduitRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Date;
use function Sodium\randombytes_random16;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Knp\Component\Pager\PaginatorInterface;
/*use Captcha\Bundle\CaptchaBundle\Validator\Constraints\ValidCaptcha;
use Gregwar\CaptchaBundle\Type\CaptchaType;
use Gregwar\CaptchaBundle\CaptchaBuilderInterface;*/
//use App\Form\CaptchaType;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManager;
use Endroid\QrCode\Builder\BuilderInterface;
use Endroid\QrCodeBundle\Response\QrCodeResponse;
use Endroid\QrCodeBundle\EndroidQrCodeBundle;






class PanierController extends AbstractController
{
/**
     * @Route("/panier", name="panier")
     */
    public function index(PanierRepository $repository , Request $request , PaginatorInterface $paginator,): Response
    {
        $d = $repository->findBy(['utilisateur'=>1])[0];
        $sum = $d->getProduits()->count();
        $data = $d->getProduits()->toArray();
        $total=0.0;

        $produits = $paginator->paginate(
            $data,
            $request->query->getInt('page',1),//num page
            3
        );
        foreach ($data as $p){
            $total += ($p->getPrix() * $p->getQuantite());
            
        }

        return $this->render('panier/index.html.twig', [
             'sumP'=>$sum , 'total'=>$total , 'data' => $produits ,
        ]);
    }

   /**
     * @Route("/panierToCommande{idUtilisateur}", name="panierToCommande")
     */

     public function panierToCommande(CommandeProduitRepository $commandeProduitRepository,ProduitRepository $produitRepository,UserRepository $utilisateurRepository,$idUtilisateur ,PanierRepository $repository, Request $request): Response
     {
 
         $newCommande = new Commande();
         $prixTot=0;
         $panier = $repository->findBy(['utilisateur' => $idUtilisateur])[0];

 if ($panier->getProduits()->toArray() == []) {
    // $session->set('empty', 'Remplir votre panier avant de passer une commande !');
    //$session->getFlashBag()->add('empty', 'Remplir votre panier avant de passer une commande !');
    $this->addFlash(
        'empty',
        'Remplir votre panier avant de passer une commande !'
    );
    return $this->redirectToRoute('panier');
} else {
 foreach($panier->getProduits()->toArray() as $p){
     $pr = $produitRepository->find($p->getId());
     $prixTot =$prixTot+ $pr->getPrix();
 }
 $newCommande->setUtilisateur($utilisateurRepository->find($idUtilisateur));
 $newCommande->setStatus("En Attente");
 $newCommande->setReference(random_int(90000,9999999) );
 $newCommande->setMontant($prixTot+7);
 $newCommande->setDateCreation(new \DateTime());
 $em = $this->getDoctrine()->getManager();
 $em->persist($newCommande);
 foreach($panier->getProduits()->toArray() as $p){
     $pr = $produitRepository->find($p->getId());
     $commandeProduit = new CommandeProduit();
     $commandeProduit->setCommande($newCommande);
     $commandeProduit->setProduit($pr);
     $commandeProduit->setQuantiteProduit($pr->getQuantite());
     $em->persist($commandeProduit);
     $newCommande->addCommandeProduit($commandeProduit);
     $pr->setQuantite(1);
     $panier->removeProduit($p);
     
 }
 $this->addFlash(
    'addCommande',
    'Votre commande a été crée avec succes, en attendant la confirmation par téléphone'
);
}
$em->flush();
return $this->redirectToRoute('commande');
}
    
     /**
      * @Route("/removeP{id}", name="removeP")
      */
     public function removeP( $id,PanierRepository $repository , Request $request): Response
     {
         $d = $repository->findBy(['utilisateur'=>1])[0];
             $em = $this->getDoctrine()->getManager();
                 $produit = $this->getDoctrine()->getRepository(Produit::class)->find($id);
                 $d->removeProduit($produit);
                 $produit->setQuantite(1);
                 $em->flush();
                 $this->addFlash(
                    'delP',
                    ' Produit supprimer avec succés'
                );
 
             // dd($newPanier->getProduits());
 
             //$em->persist($newPanier);
 
             return $this->redirectToRoute('panier');
     }
     /**
     * @Route("/ajoutProduit{id}", name="ajoutProduit1")
     */
    public function ajoutProduit( ProduitRepository $produitRepository,$id,PanierRepository $repository , Request $request): Response
    {
        
    
        $d = $repository->findBy(['utilisateur'=>1])[0];
        $em = $this->getDoctrine()->getManager();
        $produit = $produitRepository->find($id);
        $d->addProduit($produit);

        $em->flush();
        $this->addFlash(
            'addProduit',
            'Produit ajouter avec success !'
        );
        return $this->redirectToRoute('panier');
    }
    //****************************MOBILE************************************** */

    /**
     * @Route("/remplirPanier", name="remplirPanier")
     */
    public function remplirPanier(EntityManager $manager,Request $request,PanierRepository $panierRepository , SerializerInterface $serializer)
    {
        $panier = $panierRepository->find($request->get('id'));
        $panier->addProduit($request->query->get('produits'));
        $manager->flush();
        $dataJson=$serializer->serialize($panier,'json',['groups'=>'produit']);
        // dd($dataJson);
        return new JsonResponse(json_decode($dataJson) );

    }
    /**
     * @Route("/removeProduit", name="removeProduit")
     */
    public function removePanier(ProduitRepository $produitRepository,EntityManager $manager,Request $request,PanierRepository $panierRepository , SerializerInterface $serializer)
    {
        $panier = $panierRepository->find($request->get('id'));
        $produit = $produitRepository->find($request->query->get('produit'));
        $panier->removeProduit($produit);
        $manager->flush();
        $dataJson=$serializer->serialize("produit retiré avec succes");
        // dd($dataJson);
        return new JsonResponse(json_decode($dataJson) );

    }

    /**
     * @Route("/newCommande", name="newCommande")
     */
    public function newCommande(ProduitRepository $produitRepository,UserRepository $utilisateurRepository,EntityManager $manager,Request $request,PanierRepository $panierRepository , SerializerInterface $serializer)
    {
        $panier = $panierRepository->find($request->get('id_panier'));
        foreach ($panier->getProduits()->toArray() as $p) {

            $pr = $produitRepository->find($p->getId());
            $prixTot = $prixTot + ($pr->getPrix()*$pr->getQuantite());
        }
        $commande = new Commande();

        $user = $utilisateurRepository->find($request->query->get('id_utilisateur')[1]);
        $commande->setUtilisateur($user);
        $commande->setStatus("En Attente");
        $commande->setReference(random_int(90000,9999999) );
        $commande->setMontant($prixTot+7);
        $commande->setDateCreation(new \DateTime());
        $em = $this->getDoctrine()->getManager();
        foreach ($panier->getProduits()->toArray() as $p) {
            $pr = $produitRepository->find($p->getId());
            $commandeProduit = new CommandeProduit();
            $commandeProduit->setCommande($commande);
            $commandeProduit->setProduit($pr);
            $commandeProduit->setQuantiteProduit($pr->getQuantite());
            $em->persist($commandeProduit);
            $commande->addCommandeProduit($commandeProduit);
            $pr->setQuantite(1);
            $panier->removeProduit($p);
        }
        $em->flush();
        $dataJson=$serializer->serialize($commande,'json',['groups'=>'commande']);
        // dd($dataJson);
        return new JsonResponse(json_decode($dataJson) );

    }
   
}
