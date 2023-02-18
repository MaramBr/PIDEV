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

use App\Services\CartServices;





class PanierController extends AbstractController
{
    private $cartServices;
    public function __construct(CartServices $cartServices)
    {
        $this->cartServices = $cartServices;
    }
    /**
     * @Route("/cart", name="panier")
     */
    public function index(): Response
    {
        $cart = $this->cartServices->getFullCart();
        if (!isset($cart['products'])){
            return $this->redirectToRoute("app");
        }
        //$cartServices->addToCart(4);
        //dd($cartServices->getCart());//test ok pour affichage
        return $this->render('panier/index.html.twig', [
            'cart' => $cart
        ]);
    }
    /**
     * @Route("/cart/add/{id}", name = "cartAdd")
     */
    public function addToCart($id): Response{
        //$cartServices->deleteCart();
        $this->cartServices->addToCart($id);
        //dd($cartServices->getFullCart());//test ok pour l'ajout
        return $this->redirectToRoute("app");
        //return $this->render('cart/index.html.twig', [
           // 'controller_name' => 'CartController',]);
    }
       /**
     * @Route("/cart/add2/{id}", name = "cartAdd2")
     */
    public function addToCart2($id): Response{
        //$cartServices->deleteCart();
        $this->cartServices->addToCart($id);
        //dd($cartServices->getFullCart());//test ok pour l'ajout
        return $this->redirectToRoute("panier");
        //return $this->render('cart/index.html.twig', [
           // 'controller_name' => 'CartController',]);
    }

    /**
     * @Route("/cart/delete/{id}", name = "cartDelete")
     */
    public function deleteFromCart($id): Response{
       
        $this->cartServices->deleteFromCart($id);
        //dd($cartServices->getFullCart());//test ok pour le delete
        return $this->redirectToRoute("panier");
        //return $this->render('cart/index.html.twig', [
        //    'controller_name' => 'CartController',]);
    }
     /**
     * @Route("/cart/deleteAll/{id}", name = "cartDeleteAll")
     */
    public function deleteAllToCart($id): Response{
       
        $this->cartServices->deleteAllToCart($id);
        //dd($cartServices->getFullCart());//test ok pour le delete
        return $this->redirectToRoute("panier");
        //return $this->render('cart/index.html.twig', [
        //    'controller_name' => 'CartController',]);
    }

   /**
     * @Route("/panierToCommande{idUtlisateur}", name="panierToCommande")
     */

     public function panierToCommande(CartServices $cartServices,CommandeProduitRepository $commandeProduitRepository,ProduitRepository $produitRepository,UserRepository $utilisateurRepository,$idUtlisateur ,PanierRepository $panierrepository, Request $request): Response
     {
        $cart = $this->cartServices->getFullCart();
        


         $newCommande = new Commande();
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
