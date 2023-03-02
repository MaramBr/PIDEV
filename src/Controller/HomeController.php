<?php

namespace App\Controller;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Form\UserType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;
use App\Form\RegistrationFormType;
use Symfony\Component\HttpFoundation\Session\Session;

class HomeController extends AbstractController
{    /**
    * @Route("/", name="home")
    */
   public function home(Session $session)
   {
           $return = [];

           if($session->has('message'))
           {
                   $message = $session->get('message');
                   $session->remove('message'); //on vide la variable message dans la session
                   $return['message'] = $message; //on ajoute à l'array de paramètres notre message
           }
           return $this->render('fit/sanslogin.html.twig', $return);
   }


      /**
         * @Route("/membre", name="membre")
         */

   public function membre(Session $session)
        {
                $return = [];

                if($session->has('message'))
                {
                        $message = $session->get('message');
                        $session->remove('message'); //on vide la variable message dans la session
                        $return['message'] = $message; //on ajoute à l'array de paramètres notre message
                }
                return $this->render('fit/index.html.twig', $return);
        }

    // #[Route('/home', name: 'home')]
    // public function home(): Response
    // {  $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    //     return $this->render('fit/index.html.twig', [
    //         'controller_name' => 'HomeController',
    //     ]);
    // }
    #[Route('/', name: 'app_index')]
    public function index(): Response
    {
        return $this->redirectToRoute('app_fit');
   
    }
    #[Route('/fit', name: 'app_fit')]
    public function front(): Response
    {
        return $this->render('fit/sanslogin.html.twig', [
            'controller_name' => 'FitController',
        ]);
    }

    #[Route('/back', name: 'app_fitback')]
    public function back(): Response
    {
        return $this->render('fit/back.html.twig', [
            'controller_name' => 'FitController',
        ]);
    }

     /**
         * @Route("/admin", name="admin")
         */
        public function admin(Session $session,UserRepository $repo,Request $request)
        {
                $utilisateur = $this->getUser();
                if(!$utilisateur)
                {
                        $session->set("message", "Merci de vous connecter");
                        return $this->redirectToRoute('app_login');
                }

                else if(in_array('ROLE_ADMIN', $utilisateur->getRoles())){
                        $user=$repo->FindAll();
       
    
      
                        return $this->render('user/userback.html.twig',array("user"=>$user));

                        // return $this->render('fit/back.html.twig');
                }
                $session->set("message", "Vous n'avez pas le droit d'acceder à la page admin vous avez été redirigé sur cette page");
                if($session->has('message'))
                {
                        $message = $session->get('message');
                        $session->remove('message'); //on vide la variable message dans la session
                        $return['message'] = $message; //on ajoute à l'array de paramètres notre message
                }
                return $this->redirectToRoute('home', $return);

        }
 /**
     * @Route("/user/{id}/enable", name="user_enable")
     */
    public function enableUser(User $user): Response
    {
        $user->setisActive(true);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('listing');
    }

    /**
     * @Route("/user/{id}/disable", name="user_disable")
     */
    public function disableUser(User $user): Response
    {
        $user->setisActive(0);
        $now = new \DateTime();
        $disabledUntil = clone $now;
        $disabledUntil->modify('+2 minute');

        $user->setDisabledUntil($disabledUntil);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('listing');
    }
    // #[Route('/backuser', name: 'backuser')]
    // public function backuser(): Response
    // {
    //     return $this->render('user/userback.html.twig', [
    //         'controller_name' => 'FitController',
    //     ]);
    // }
  

  #[Route('/listing', name: 'listing')]
    public function listing1(UserRepository $repo,Request $request)
    { 
        

       $user=$repo->FindAll();
       
    
      
       return $this->render('user/userback.html.twig',array("user"=>$user));
}
#[Route('/monprofile/{id}', name: 'monprofile')]
public function listing2(UserRepository $repo,Request $request,$id)
{ 
    

        $user=$repo->find($id);   

  
   return $this->render('user/userback2.html.twig',[
        'user2' => $user,
   ]);
}


#[Route("/ALLUsers", name: "listUser")]
    public function getUsers(UserRepository $repo, NormalizerInterface $normalizer)
    {
        $users = $repo->findAll();
        $usersNormalises  = $normalizer->normalize($users, 'json', ['groups' => "users"]);
        $json = json_encode($usersNormalises);
        return new Response($json);
    }
    #[Route("/Users/{id}", name: "User")]
    public function UserId($id, UserRepository $repo, NormalizerInterface $normalizer)
    {
        $users = $repo->find($id);
        $usersNormalises  = $normalizer->normalize($users, 'json', ['groups' => "users"]);
        $json = json_encode($usersNormalises);
        return new Response($json);
    }
    #[Route("/addUserJSON/new", name: "addUserJSON")]
    public function addUserJSON(Request $req, NormalizerInterface $Normalizer)
    {
        $em = $this->getDoctrine()->getManager();
        $user = new User();
        $user->setNom($req->get('nom'));
        $user->setAdresse($req->get('adresse'));
        $user->setEmail($req->get('email'));
        $hashedPassword = password_hash($req->get('password'), PASSWORD_DEFAULT);
        $user->setPassword($hashedPassword);
        $user->setImage($req->get('image'));
        $em->persist($user);
        $em->flush();

        $jsonContent = $Normalizer->normalize($user, 'json', ['groups' => 'users']);
        return new Response(json_encode($jsonContent));
    }
    #[Route("/updateUserJSON/{id}", name: "updateUserJSON")]
    public function updateUserJSON(Request $req, $id, NormalizerInterface $Normalizer)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($id);
        $user->setNom($req->get('nom'));
        $user->setAdresse($req->get('adresse'));
        $user->setEmail($req->get('email'));
        $user->setImage($req->get('image'));
        $em->flush();
        $jsonContent = $Normalizer->normalize($user, 'json', ['groups' => 'users']);
        return new Response("User updated successfully " . json_encode($jsonContent));
    }
    #[Route("/deleteUserJSON/{id}", name: "deleteUserJSON")]
    public function deleteUserJSON(Request $req, $id, NormalizerInterface $Normalizer)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($id);
        $em->remove($user);
        $em->flush();
        $jsonContent = $Normalizer->normalize($user, 'json', ['groups' => 'users']);
        return new Response("User deleted successfully " . json_encode($jsonContent));
    }


}


