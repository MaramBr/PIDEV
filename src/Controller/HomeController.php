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
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\PieChart;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Knp\Component\Pager\PaginatorInterface;

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
                // $return = [];

                // if($session->has('message'))
                // {
                //         $message = $session->get('message');
                //         $session->remove('message'); //on vide la variable message dans la session
                //         $return['message'] = $message; //on ajoute à l'array de paramètres notre message
                // }
                if ($this->isGranted('ROLE_USER')  ) {

                return $this->render('fit/index.html.twig');
                }
                {
                    return $this->render('/403.html.twig');
                }


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
    {                 if ($this->isGranted('ROLE_ADMIN')  ) {

        return $this->render('fit/back.html.twig', [
            'controller_name' => 'FitController',
        ]);
    }
    else
    {
        return $this->render('/403.html.twig');
    }

    }

     /**
         * @Route("/admin", name="admin")
         */
        public function admin(Session $session,UserRepository $repo,Request $request)
        {
               
                if ($this->isGranted('ROLE_ADMIN')  ) {
                $user=$repo->FindAll();
       
    
      
                return $this->render('user/userback.html.twig',array("user"=>$user));
                }
                else
                {
                    return $this->render('/403.html.twig');
                }

    
        }


    //  /**
    //      * @Route("/coach", name="coach")
    //      */
    //     public function coach(Session $session,UserRepository $repo,Request $request)
    //     {
               
    //             if ($this->isGranted('ROLE_Coach') ) {
    //             $user=$repo->FindAll();
       
    
      
    //             return $this->render('user/userback.html.twig',array("user"=>$user));
    //             }
    //             else
    //             {
    //                 return $this->render('/403.html.twig');
    //             }

    
    //     }

 
    // #[Route('/backuser', name: 'backuser')]
    // public function backuser(): Response
    // {
    //     return $this->render('user/userback.html.twig', [
    //         'controller_name' => 'FitController',
    //     ]);
    // }
  

  #[Route('/listing', name: 'listing')]
    public function listing1(UserRepository $repo,Request $request,PaginatorInterface $paginator )
    { 
        
        if ($this->isGranted('ROLE_ADMIN')) {
       $user=$repo->FindAll();
       $data= $paginator->paginate(
        $user,
        $request->query->getInt('page',1),//num page
        3
    );
        
 

      
       return $this->render('user/userback.html.twig',[
        'user' => $data,
    ]);
        }
        else
        {
            return $this->render('/403.html.twig');
        }
}


// #[Route('/listing', name: 'listing')]
// public function listing1(UserRepository $repo,Request $request)
// { 
    
//     if ($this->isGranted('ROLE_ADMIN')) {
//    $user=$repo->FindAll();
   

  
//    return $this->render('user/userback.html.twig',array("user"=>$user));
//     }
//     else
//     {
//         return $this->render('/403.html.twig');
//     }
// }
    
    
#[Route('/search1', name: 'search1')]
public function searchEvenementx(Request $request, NormalizerInterface $Normalizer,UserRepository $sr)
{
    $repository = $this->getDoctrine()->getRepository(User::class);
    $requestString = $request->get('searchValue');
    $Evenements = $repository->findEvenementByNom($requestString);
    $jsonContent = $Normalizer->normalize($Evenements, 'json', ['groups' => 'User']);
    $retour = json_encode($jsonContent);
    return new Response($retour);
}
  /**
     * @Route("/stat3" , name="stat3")
     */
    public function stat(UserRepository $repository)
    {
        //**************************** Iheb ***************************
        $roleuser = $repository->roleuser();
        $roleadmin = $repository->roleadmin();
        $rolecoach = $repository->rolecoach();
        // dd($roleuser);

        $IhebChart1 = new PieChart();
        $IhebChart1->getData()->setArrayToDataTable(
            [['Task', 'Hours per Day'],
                ['roleuser',(int)( $roleuser)],
                ['roleadmin',(int)($roleadmin)],
                ['rolecoach',(int)($rolecoach)],
            ]
        );
        $IhebChart1->getOptions()->setTitle("LES UTLISATEURS DE E-FIT");
        $IhebChart1->getOptions()->setHeight(400);
        $IhebChart1->getOptions()->setIs3D(2);
        $IhebChart1->getOptions()->setWidth(550);
        $IhebChart1->getOptions()->getTitleTextStyle()->setBold(true);
        $IhebChart1->getOptions()->getTitleTextStyle()->setColor('#009900');
        $IhebChart1->getOptions()->getTitleTextStyle()->setItalic(true);
        $IhebChart1->getOptions()->getTitleTextStyle()->setFontName('Arial');
        $IhebChart1->getOptions()->getTitleTextStyle()->setFontSize(15);

        return $this->render('user/stats.html.twig', array(
            'IhebChart1' => $IhebChart1 ,
          ));
    



    }

#[Route('/monprofile/{id}', name: 'monprofile')]
public function listing2(UserRepository $repo,Request $request,$id)
{ 
    

        $user=$repo->find($id);   

  
   return $this->render('user/userback2.html.twig',[
        'user2' => $user,
   ]);
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
/*
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
*/
///////////////////////////////////////////////////////////////////////////////////////////

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
        $user->setPrenom($req->get('prenom'));
        $user->setEmail($req->get('email'));
        $hashedPassword = password_hash($req->get('password'), PASSWORD_DEFAULT);
        $user->setPassword($hashedPassword);
        //$user->setPassword($req->get('password'));
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

 
    // /**
    //  * @Route("/loginJson" , name="loginn")
    //  */
    // public function loginJson(Request $request, NormalizerInterface $normalizer)
    // {
    //     $email = $request->query->get("email");
    //     $password = $request->query->get("password");
    //     $em = $this->getDoctrine()->getManager();
    //     $user = $em->getRepository(User::class)->findOneBy(['email' => $email]);
    //     if ($user) {
    //         if (password_verify($password, $user->getPassword())) {

    //             $jsonContent = $normalizer->normalize($user, 'json', ['groups' => 'users']);
    //             return new Response(json_encode($jsonContent));
    //         } else {
    //             return new Response("password not found");
    //         }
    //     } else {
    //         return new Response("user not found");
    //     }
    // }
    /**
     * @Route("/ediUser",name="app_pro")
     */

    public function editUser(Request $request)
    {
        $id = $request->get("id");
        $email = $request->query->get("email");
        $password = $request->query->get("password");
        $name = $request->query->get("nom");
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($id);
        if ($request->files->get("image") != null) {
            $file = $request->files->get("image");
            $fileName = $file->getClientOriginalName();
            $file->move(
                $fileName
            );
            $user->setImage($fileName);
        }
        $user->setNom($name);
       // $user->setPassword($password);
        $user->setPassword(
                 $encoder->encodePassword(
                   $user,
                       $password
        )
           );
        $user->setEmail($email);
        $user->setIsVerified(true);
        try {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            return new JsonResponse("success", 200);
        } catch (\Exception $ex) {
            return new Response("fail", $ex->getMessage());
        }
    }

    /**
     * @Route("/getPasswordByEmail", name="password")
     */
    public function getPasswordByEmail(Request $request, NormalizerInterface $normalizer,UserPasswordEncoderInterface $passwordEncoder){
    /*
        $email = $request->get('email');
        $user = $this->getDoctrine()->getManager()->getRepository(User::class)->findOneBy(['email' => $email]);
        if ($user) {
            $password = $user->getPassword();
            //$serializer = new Serialiser([new ObjectNormalizer()]);
            //$formatted =$serializer->normalize($password);
            //return new JsonResponse($formatted);
            $jsonContent = $normalizer->normalize($password, 'json', ['groups' => 'users']);
            return new Response(json_encode($jsonContent));
        }
        return new Response("user not found");*/
        $email = $request->get('email');
        $user = $this->getDoctrine()->getManager()->getRepository(User::class)->findOneBy(['email' => $email]);
        if ($user) {
          
            $newPassword = substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyz', ceil(6/strlen($x)) )),1,6);
            $encodedPassword = $passwordEncoder->encodePassword($user, $newPassword);
            $user->setPassword($encodedPassword);
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $jsonContent = $normalizer->normalize($newPassword, 'json', ['groups' => 'users']);
            return new Response(json_encode($jsonContent));
        }
        return new Response("user not found");
    }

    /**
 * @Route("/uploadUserPic/", name="uploadUserPic")
 */
public function uploadUserPic(UserRepository $rep)
{
    $allowedExts = array("jpeg", "jpg", "png");
    $temp = explode(".", $_FILES["file"]["name"]);
    $extension = end($temp);

    if ((($_FILES["file"]["type"] == "image/jpeg") || ($_FILES["file"]["type"] == "image/jpg") || ($_FILES["file"]["type"] == "image/png")) && ($_FILES["file"]["size"] < 5000000) && in_array($extension, $allowedExts)) {
        if ($_FILES["file"]["error"] > 0) {
            $named_array = array("Response" => array(array("Status" => "error")));
            echo json_encode($named_array);
        } else {
            $destination_path = "C:/xampp/htdocs/PIDEV(Finale)/PIDEV/public/uploads/" . $_FILES["file"]["name"];
            move_uploaded_file($_FILES["file"]["tmp_name"], $destination_path);
            $named_array = array("Response" => array(array("Status" => "ok")));

           
            echo json_encode($named_array);
        }
    } else {
        $named_array = array("Response" => array(array("Status" => "invalid")));
        echo json_encode($named_array);
    }
}
    #[Route("/orderbyadresse", name: "orderbyadresse", methods: ['GET'])]

    public function orderbyadresse(Request $request, UserRepository $repo): Response
    {
        $org = $repo->orderbyadresse();

        return $this->render('user/traitement.html.twig', [
            'test' => $org,
        ]);
    }


    // /**
    //  * @Route("/editProfileJson" , name="app_gestion_profile")
    //  */
    // public function editUser1(Request $request, UserPasswordEncoderInterface $encoder)
    // {
    //     $id = $request->get("id");
    //     $username = $request->query->get("email");
    //     $password = $request->query->get("password");
    //     $nom = $request->query->get("nom");
    //     $adresse = $request->query->get("adresse");

    //     $em = $this->getDoctrine()->getManager();
    //     $user = $em->getRepository(User::class)->find($id);
    //     if ($request->files->get("image") != null) {
    //         $file = $request->files->get("image");
    //         $fileName = $file->getClientOriginalName();
    //         $file->move($fileName);
    //         $user->setImage($fileName);
    //     }
    //     $user->setEmail($username);
    //     $user->setPassword(
    //         $encoder->encodePassword(
    //             $user,
    //             $password
    //         )
    //     );
    //     $user->setNom($nom);
    //     $user->setAdresse($adresse);

    //     //$user->setIsVerified(true);
    //     try {
    //         $em = $this->getDoctrine()->getManager();
    //         $em->persist($user);
    //         $em->flush();
    //         return new JsonResponse("success", 200); //200 yaani http result ta3 server ok
    //     } catch (\Exception $ex) {
    //         return new Response("failed" . $ex->getMessage());
    //     }
    // }
    
    /**
     * @Route("/loginJson" , name="loginn")
     */
    public function loginJson(Request $request, NormalizerInterface $normalizer)
    {/*
        $email = $request->query->get("email");
        $password = $request->query->get("password");
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->findOneBy(['email' => $email]);
        if ($user) {
            if ($password  ==  $user->getPassword()) {

                $jsonContent = $normalizer->normalize($user, 'json', ['groups' => 'users']);
                return new Response(json_encode($jsonContent));
            } else {
                return new Response("password not found");
            }
        } else {
            return new Response("user not found");
        }*/
        
        
            $email = $request->query->get("email");
            $password = $request->query->get("password");
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository(User::class)->findOneBy(['email' => $email]);
            if ($user) {
                if (password_verify($password, $user->getPassword())) {
    
                    $jsonContent = $normalizer->normalize($user, 'json', ['groups' => 'users']);
                    return new Response(json_encode($jsonContent));
                } else {
                    return new Response("password not found");
                }
            } else {
                return new Response("user not found");
            }
        
    }

  /**
     * @Route("/editProfileJson" , name="app_gestion_profile")
     */
    public function editUser1(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $id = $request->get("id");
        $username = $request->query->get("email");
        $password = $request->query->get("password");
        $nom = $request->query->get("nom");
        $adresse = $request->query->get("prenom");

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($id);
        if ($request->files->get("image") != null) {
            $file = $request->files->get("image");
            $fileName = $file->getClientOriginalName();
            $file->move($fileName);
            $user->setImage($fileName);
        }
        $user->setEmail($username);
        //$user->setPassword($request->get('password'));
        $user->setPassword(
            $encoder->encodePassword(
                $user,
                $password
            )
        );
        $user->setNom($nom);
        $user->setPrenom($adresse);

        //$user->setIsVerified(true);
        try {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            return new JsonResponse("success", 200); //200 yaani http result ta3 server ok
        } catch (\Exception $ex) {
            return new Response("failed" . $ex->getMessage());
        }
    }
    /*
    public function getPasswordByEmail(Request $request, NormalizerInterface $normalizer ,UserPasswordEncoderInterface $passwordEncoder)
    {
        $email = $request->get('email');
        $user = $this->getDoctrine()->getManager()->getRepository(User::class)->findOneBy(['email' => $email]);
        if ($user) {
          
            $newPassword = substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyz', ceil(6/strlen($x)) )),1,6);
            $encodedPassword = $passwordEncoder->encodePassword($user, $newPassword);
            $user->setPassword($encodedPassword);
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $jsonContent = $normalizer->normalize($newPassword, 'json', ['groups' => 'users']);
            return new Response(json_encode($jsonContent));
        }
        return new Response("user not found");
    }*/



}


