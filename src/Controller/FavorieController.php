<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\User;
use App\Entity\Favorie;
use App\Entity\Commande;
use App\Entity\CommandeProduit;
use App\Entity\Panier;
use App\Entity\Produit;
use App\Repository\CommandeProduitRepository;
use App\Repository\FavorieRepository;
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

class FavorieController extends AbstractController
{
   /**
     * @Route("/favorie", name="app_favorie")
     */
    public function index(PanierRepository $repository , Request $request , PaginatorInterface $paginator): Response
    {
        

        $total=0;
        $data=[];
        $sum=0;
        
        // $this->addFlash();
        $d = $repository->findBy(['utilisateur'=>1])[0];
        $sum = $d->getProduits()->count();

        $produit = $this->getDoctrine()->getRepository(Favorie::class)->findBy(['utilisateur'=>1])[0];
        $data = $produit->getProduit();
        $produits = $paginator->paginate(
            $data,
            $request->query->getInt('page',1),//num page
            2
        );
        foreach ($d->getProduits() as $p){
            $total += ($p->getPrix() * $p->getQuantite());
        }
        return $this->render('favorie/index.html.twig', [
            'data' => $produits , 'sumP'=>$sum , 'total'=>$total
        ]);
    }

    /**
     * @Route("/removePF{id}", name="removePF")
     */
    public function removeP( $id,FavorieRepository $repository , Request $request): Response
    {
       
        $d = $repository->findBy(['utilisateur'=>1])[0];
        $em = $this->getDoctrine()->getManager();
        $produit = $this->getDoctrine()->getRepository(Produit::class)->find($id);
        $d->removeProduit($produit);
        $em->flush();
        $this->addFlash(
            'delP',
            ' Produit Favorie Retiré avec succés'
        );


        // dd($newPanier->getProduits());

        //$em->persist($newPanier);

        return $this->redirectToRoute('app_favorie');

    }

     /**
     * @Route("/ajoutProduitF{id}", name="ajoutProduitF")
     */
    public function ajoutProduit( ProduitRepository $produitRepository,$id,FavorieRepository $repository , Request $request): Response
    {
      
        $d = $repository->findBy(['utilisateur'=>1])[0];
        $em = $this->getDoctrine()->getManager();
        $produit = $produitRepository->find($id);
        $d->addProduit($produit);
        $em->flush();
        $this->addFlash(
            'addProduit',
            'Produit Favorie ajouter avec success !'
        );

        return $this->redirectToRoute('app_favorie');

    }



}
