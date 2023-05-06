<?php

namespace App\Controller;
use App\Form\PwdType;
use App\Form\UpdateType;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Form\AdminType;
use App\Form\EditType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\UserRepository;


/**
* @Route("/utilisateur")
*/

class RegistrationController extends AbstractController
{


    /**
        * @Route("/", name="utilisateur_index", methods={"GET"})
        */
        public function index(UserRepository $utilisateurRepository, Session $session): Response
        {
                //besoin de droits admin
                $utilisateur = $this->getUser();
                if(!$utilisateur)
                {
                        $session->set("message", "Merci de vous connecter");
                        return $this->redirectToRoute('app_login');
                }

                else if(in_array('ROLE_ADMIN', $utilisateur->getRoles())){
                        return $this->render('utilisateur/index.html.twig', [
                                'utilisateurs' => $utilisateurRepository->findAll(),
                        ]);
                }

                return $this->redirectToRoute('home');
        }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager,Session $session): Response
    {
       //test de sécurité, un utilisateur connecté ne peut pas s'inscrire
       $utilisateur = $this->getUser();
       if($utilisateur)
       {
               $session->set("message", "Vous ne pouvez pas créer un compte lorsque vous êtes connecté");
               return $this->redirectToRoute('membre');
       }


        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
           
            $uploadedFile = $form['Image']->getData();
            $destination = $this->getParameter('kernel.project_dir').'/public/uploads';
            $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
            $newFile = $originalFilename.'-'.uniqid().'.'.$uploadedFile->guessExtension();
            $uploadedFile->move(
                $destination,
                $newFile
               );
               $user->setImage($newFile);
     
          
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_fit');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
            'utilisateur' => $user,
        ]);
    
    }
    
    #[Route('/registerback', name: 'app_registerback')]
    public function registerback(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {  if ($this->isGranted('ROLE_ADMIN')) {
        $user = new User();
        $form = $this->createForm(AdminType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
        //   dd($use);
            $uploadedFile = $form['Image']->getData();
            $destination = $this->getParameter('kernel.project_dir').'/public/uploads';
            $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
            $newFile = $originalFilename.'-'.uniqid().'.'.$uploadedFile->guessExtension();
            $uploadedFile->move(
                $destination,
                $newFile
               );
               $user->setImage($newFile);
          
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $this->redirectToRoute('listing');
        }

        return $this->render('registration/registerback.html.twig', [
            'formback' => $form->createView(),
        ]);
    }
    else {
        return $this->render('/403.html.twig');
    }
    }

    
   

    #[Route('/update/{id}', name: 'updateuser')]
     
    function update(UserRepository $repository, $id, Request $request,UserPasswordHasherInterface $userPasswordHasher,Session $session, User $utilisateur)
    { if ($this->isGranted('ROLE_ADMIN')) {
        // $utilisateur = $this->getUser();
        // if($utilisateur->getId() != $id )
        // {
        //         // un utilisateur ne peut pas en modifier un autre
        //         $session->set("message", "Vous ne pouvez pas modifier cet utilisateur");
        //         return $this->redirectToRoute('membre');
        // }
        $user=$repository->find($id);
        $form=$this->createForm(UpdateType ::class,$user);
        
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            // $uploadedFile = $form['Image']->getData();
            // $destination = $this->getParameter('kernel.project_dir').'/public/uploads';
            // $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
            // $newFile = $originalFilename.'-'.uniqid().'.'.$uploadedFile->guessExtension();
            // $uploadedFile->move(
            //     $destination,
            //     $newFile
            //    );
            //    $user->setImage($newFile);

         
            // $user->setPassword(
            //     $userPasswordHasher->hashPassword(
            //         $user,
            //         $form->get('password')->getData()
            //     )
            // );


//    $user->setPassword(
//                 $userPasswordHasher->hashPassword(
//                     $user,
//                     $form->get('password')->getData()
//                 ));

            $em=$this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush(); 
            return $this->redirectToRoute('listing'); 
        }
        // else
        // var_dump("aasa");
    //     return $this->render('user/updateback.html.twig', 
    //    ['formeuser'=>$form->createView()]); 
return $this->renderForm('user/updateback.html.twig',array("form"=>$form));
    }
    else {
        return $this->render('/403.html.twig');
    }
    } 

    #[Route('/delete/{id}', name: 'deleteuser')]
    public function deleteproduct(ManagerRegistry $mg,UserRepository $userRepository,$id,)
    {
        if ($this->isGranted('ROLE_ADMIN')) {
   $user=$userRepository->find($id);
    

  
    $em=$mg->getManager();
    $em->remove($user);
    $em->flush();
return $this->redirectToRoute('listing');
        }
        else {
            return $this->render('/403.html.twig');
        }
    }



  /**
         * @Route("/{id}", name="utilisateur_delete", methods={"DELETE"})
         */
        public function delete(Request $request, User $utilisateur, Session $session, $id): Response
        {         if ($this->isGranted('ROLE_ADMIN')) {

                $utilisateur = $this->getUser();
                if($utilisateur->getId() != $id )
                {
                        // un utilisateur ne peut pas en supprimer un autre
                        $session->set("message", "Vous ne pouvez pas supprimer cet utilisateur");
                        return $this->redirectToRoute('membre');
                }

                if ($this->isCsrfTokenValid('delete'.$utilisateur->getId(), $request->request->get('_token')))
                {
                        $entityManager = $this->getDoctrine()->getManager();
                        $entityManager->remove($utilisateur);
                        $entityManager->flush();
                        // permet de fermer la session utilisateur et d'éviter que l'EntityProvider ne trouve pas la session
                        $session = new Session();
                        $session->invalidate();
                }

                return $this->redirectToRoute('home');
            }
            else {
                return $this->render('/403.html.twig');
            }

        }


#[Route('/sign', name: 'app_sign')]
public function sign(): Response
{
    return $this->render('user/sign.html.twig', [
        'controller_name' => 'FitController',
    ]);
}

#[Route('/showprofile', name: 'showprofile')]
public function front(): Response
{
    return $this->render('fit/profil.html.twig', [
        'controller_name' => 'FitController',
    ]);
}



#[Route('/editprofile', name: 'editprofile')]
public function editprofile(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
{
    $user = $this->getUser();
    $form = $this->createForm(EditType::class, $user);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $uploadedFile = $form['Image']->getData();
        $destination = $this->getParameter('kernel.project_dir').'/public/uploads';
        $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
        $newFile = $originalFilename.'-'.uniqid().'.'.$uploadedFile->guessExtension();
        $uploadedFile->move(
            $destination,
            $newFile
           );
           $user->setImage($newFile);
      
        // encode the plain password
       
        $entityManager=$this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();
        // do anything else you need here, like send an email
$this->addFlash('message','Profil mis a jour');
        return $this->redirectToRoute('showprofile');
    }

    return $this->render('user/editprofile.html.twig', [
        'formedit' => $form->createView(),
    ]);
}

#[Route('/editmdp', name: 'editmdp')]
public function editmdp(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
{  $user=$this->getUser();
        $form = $this->createForm(pwdType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
          
           
          
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email
$this->addFlash('message','Mot de passe mis a jour avec succés');
            return $this->redirectToRoute('showprofile');
   
    }
    return $this->render('user/editmdp.html.twig', [
        'form' => $form->createView(),
    ]);

}

}


