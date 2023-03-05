<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManager;
use App\Entity\user;
use App\Entity\Commande;
use App\Entity\Produit;
use App\Repository\CommandeRepository;
use App\Repository\PanierRepository;
use App\Repository\ProduitRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Knp\Component\Pager\PaginatorInterface;

use Endroid\QrCode\Builder\BuilderInterface;
use Endroid\QrCodeBundle\Response\QrCodeResponse;
use Endroid\QrCodeBundle\EndroidQrCodeBundle;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Dompdf\Dompdf;
use Dompdf\Options;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\PieChart;



class CommandeController extends AbstractController
{
   

    /**
     * @Route ("/commande", name="commande")
     */
    public function index(PaginatorInterface $paginator,BuilderInterface $customQrCodeBuilder,Request $req ,PanierRepository $panierRepository, CommandeRepository $repository): Response
    {
        $data = $repository->findBy(['utilisateur'=>1]);
        $d = $panierRepository->findBy(['utilisateur'=>1])[0];
        $sum = $d->getProduits()->count();
        $dataTarray = $d->getProduits()->toArray();
        $total=0.0;
        $result = $customQrCodeBuilder
            ->size(400)
            ->margin(20)
            ->build();
        $response = new QrCodeResponse($result);

        $commande = $paginator->paginate(
            $data,
            $req->query->getInt('page',1),//num page
            2
        );
        foreach ($dataTarray as $p){
            $total += ($p->getPrix() * $p->getQuantite());
        }
        return $this->render('commande/index.html.twig', [
            'data'=>$commande , 'sumP'=>$sum , 'total'=>$total , 'qr'=>$response->getContent()
        ]);
    }

    /**
     * @Route ("/commandeDelete/{id}", name="commandeDelete")
     */
    public function delete(CommandeRepository $repository , $id): Response
    {
        $comm =$repository->find($id) ;
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($comm);
        $manager->flush();
        $this->addFlash(
            'delC',
            ' Votre Commande est annulée'
        );
        //return new Response('suppression avec succes');
        return $this->redirectToRoute('commande');
    }
//***********************MOBILE*************************** */

 /**
     * @Route("/GetCommande", name="GetCommande")
     */
    public function getCommandes(CommandeRepository $repository , SerializerInterface $serializer)
    {
        $p = $repository->findAll();
        $dataJson=$serializer->serialize($p,'json',['groups'=>'commande']);
        // dd($dataJson);
        return new JsonResponse(json_decode($dataJson) );

    }

     /**
     * @Route ("/addCommande")
     */
    public function addCommande(Request $request , SerializerInterface $serializer , EntityManager $em){
        $content = $request->getContent();
        $data = $serializer->deserialize($content,Commande::class,'json');
        $em->persist($data);
        $em->flush();
        return new Response('Commande added seccesfully');
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




      
}
