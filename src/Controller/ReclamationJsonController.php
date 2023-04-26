<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Repository\ReclamationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Twilio\Rest\Client;

class ReclamationJsonController extends AbstractController
{
    #[Route('/reclamation/json', name: 'app_reclamation_json')]
    public function index(): Response
    {
        return $this->render('reclamation_json/index.html.twig', [
            'controller_name' => 'ReclamationJsonController',
        ]);
    }

    #[Route("/AllReclamations", name: "list")]
    //* Dans cette fonction, nous utilisons les services NormlizeInterface et ReclamationRepository, 
    //* avec la méthode d'injection de dépendances.
    public function getReclamations(ReclamationRepository $repo, SerializerInterface $serializer)
    {
        $Reclamations = $repo->findAll();
        //* Nous utilisons la fonction normalize qui transforme le tableau d'objets 
        //* Reclamations en  tableau associatif simple.
        // $ReclamationsNormalises = $normalizer->normalize($Reclamations, 'json', ['groups' => "Reclamations"]);

        // //* Nous utilisons la fonction json_encode pour transformer un tableau associatif en format JSON
        // $json = json_encode($ReclamationsNormalises);

        $json = $serializer->serialize($Reclamations, 'json', ['groups' => "Reclamations"]);

        //* Nous renvoyons une réponse Http qui prend en paramètre un tableau en format JSON
        return new Response($json);
    }


    #[Route("/Reclamation/{id}", name: "Reclamation")]
    public function ReclamationId($id, NormalizerInterface $normalizer, ReclamationRepository $repo)
    {
        $Reclamation = $repo->find($id);
        $ReclamationNormalises = $normalizer->normalize($Reclamation, 'json', ['groups' => "Reclamations"]);
        return new Response(json_encode($ReclamationNormalises));
    }


    #[Route("addReclamationJSON/new", name: "addReclamationJSON")]
    public function addReclamationJSON(Request $req,   NormalizerInterface $Normalizer)
    {

        $em = $this->getDoctrine()->getManager();
        $Reclamation = new Reclamation();
        $Reclamation->setTitreR($req->get('Titre'));
        $Reclamation->setDateR(new \DateTime('now'));
        $Reclamation->setDescriptionR((string) $req->get('Description'));
  

        $em->persist($Reclamation);
        $em->flush();

        $jsonContent = $Normalizer->normalize($Reclamation, 'json', ['groups' => 'Reclamations']);
        $accountSid = 'AC099883ea0f07c67cd19e55b497fceb12';
             $authToken = '7cd54f265b4727ce40c3c978b0181069';
             $client = new Client($accountSid, $authToken);
     
             $message = $client->messages->create(
                 '+21692524435', // replace with admin's phone number
                 [
                     'from' => '+12766336884', // replace with your Twilio phone number
                     'body' => 'New réclamation par Melliti Majdi ' ,
                 ]
             );
        return new Response(json_encode($jsonContent));
    }

    #[Route("updateReclamationJSON/id={id}", name: "updateReclamationJSON")]
    public function updateReclamationJSON(Request $req, $id, NormalizerInterface $Normalizer)
    {

        $em = $this->getDoctrine()->getManager();
        $Reclamation = $em->getRepository(Reclamation::class)->find($id);
        $Reclamation->setTitreR($req->get('Titre'));
        $Reclamation->setDescriptionR($req->get('Description'));

        $em->flush();

        $jsonContent = $Normalizer->normalize($Reclamation, 'json', ['groups' => 'Reclamations']);
        return new Response("Reclamation updated successfully " . json_encode($jsonContent));
    }

    #[Route("deleteReclamationJSON/{id}", name: "deleteReclamationJSON")]
    public function deleteReclamationJSON(Request $req, $id, NormalizerInterface $Normalizer)
    {

        $em = $this->getDoctrine()->getManager();
        $Reclamation = $em->getRepository(Reclamation::class)->find($id);
        $em->remove($Reclamation);
        $em->flush();
        $jsonContent = $Normalizer->normalize($Reclamation, 'json', ['groups' => 'Reclamations']);
        return new Response("Reclamation deleted successfully " . json_encode($jsonContent));
    }
}




   




