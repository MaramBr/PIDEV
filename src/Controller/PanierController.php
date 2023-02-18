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





class PanierController extends AbstractController
{
/**
     * @Route("/panier", name="cart_index")
     */
    public function index(SessionInterface $session, ProduitRepository $ProduitRepository , PanierRepository $PanierRepository )
    {

        $panier = $session->get('panier', []);
        $panierWithData = [];
        foreach ($panier as $id => $quantite  ) {
            $panierWithData[] = [
                'produit' => $ProduitRepository->find($id),
                'quantite' => $quantite,];}
        $total = 0;
        $sum=0;
        foreach ($panierWithData as $couple) {
            $total += $couple['produit']->getPrix() * $couple['quantite'];
            $sum+=$couple['quantite'];
        }
        return $this->render('panier/index.html.twig', [
            "items" => $panierWithData,
            "total" => $total,
            "sum"=>$sum,
        ]);}

    /**
     * @Route("/ajouter/{id}", name="cart_add")
     */
    public function add($id, SessionInterface $session)
    {
        $panier = $session->get('panier', []);
        if (empty($panier[$id])) {
            $panier[$id] = 0;
        }
        $panier[$id]++;
        $session->set('panier', $panier);
        return $this->redirectToRoute("app");
    }
      /**
     * @Route("/supprimer/{id}", name="cart_remove")
     */
    public function remove($id, SessionInterface $session)
    {
        $panier = $session->get('panier', []);
        if (!empty($panier[$id])) {
            unset($panier[$id]);
        }
        $session->set('panier', $panier);
        return $this->redirectToRoute('cart_index');
    }
           /**
     * @Route("/addquantite/{id}/{$quantite}", name="cart_addq")
     */
    public function addq($id, SessionInterface $session)
    {
        $panier = $session->get('panier', []);
        if (empty($panier[$id])) {
            $panier[$id] = $panier[$id];
        }
        $panier[$id]++;
        $session->set('panier', $panier);
        return $this->redirectToRoute("cart_index");
    }
     /**
     * @Route("/removequantite/{id}", name="cart_removeq")
     */
    public function removeq($id, SessionInterface $session)
    {
        $panier = $session->get('panier', []);
        if (!empty($panier[$id])) {
            $panier[$id] = $panier[$id];
        $panier[$id]--;}
        else  if (empty($panier[$id])) {
            unset($panier[$id]);
        }
        $session->set('panier', $panier);
        return $this->redirectToRoute("cart_index");
    }
   /**
     * @Route("/panierToCommande{idUtlisateur}", name="panierToCommande")
     */

     public function panierToCommande(CommandeProduitRepository $commandeProduitRepository,ProduitRepository $produitRepository,UserRepository $utilisateurRepository,$idUtlisateur ,PanierRepository $panierrepository, Request $request): Response
     {
 
         $newCommande = new Commande();
         $prixTot=0;
         $panier = $panierrepository->findBy(['utilisateur' => $idUtlisateur][0]);
         foreach($panier->getProduits()->toArray() as $p){
             $pr = $produitRepository->find($p->getId());
             $prixTot =$prixTot+ $pr->getPrix();
         }
         $newCommande->setUtilisateur($utilisateurRepository->find($idUtlisateur));
         $newCommande->setStatus("En Attente");
         $newCommande->setReference(random_bytes(10));
         $newCommande->setMontant($prixTot);
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
         $em->flush();
         return $this->redirectToRoute('commande');
     }
   
}
