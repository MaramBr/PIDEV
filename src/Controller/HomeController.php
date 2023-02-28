<?php

namespace App\Controller;

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




}


