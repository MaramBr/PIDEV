<?php

namespace App\Controller;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Session\Session;
use App\Form\ForgotPasswordType;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\UserRepository;
use App\Form\UserType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;
use App\Form\RegistrationFormType;
use Swift_Mailer;
use Swift_Message;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Mime\Email;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils,Session $session): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        // $error = $authenticationUtils->getLastAuthenticationError();
        // // last username entered by the user
        // $lastUsername = $authenticationUtils->getLastUsername();
        // return $this->render('user/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);



         // if ($this->getUser()) {
                //     return $this->redirectToRoute('target_path');
                // }

                // get the login error if there is one
                $error = $authenticationUtils->getLastAuthenticationError();
                // last username entered by the user
                $lastUsername = $authenticationUtils->getLastUsername();

                $return = ['last_username' => $lastUsername, 'error' => $error];

                if($session->has('message'))
                {
                        $message = $session->get('message');
                        $session->remove('message'); //on vide la variable message dans la session
                        $return['message'] = $message; //on ajoute à l'array de paramètres notre message
                }

                return $this->render('user/login.html.twig', $return);
       
    }

    // #[Route(path:'/logout', name: 'app_logout')]
    // public function logout(): void
    // {  

    //     throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    // }

         /**
         * @Route("/logout", name="app_logout")
         */
        public function logout()
        {
                throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
        }


private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @Route("/email1", name="email")
     */
    public function sendEmail(Request $request, MailerInterface $mailer,UserRepository $userRepository,TokenGeneratorInterface  $tokenGenerator): Response
    {

        $form = $this->createForm(ForgotPasswordType::class);
        $form->handleRequest($request);
        if($form->isSubmitted()) {
            $donnees = $form->getData();//


            $user = $userRepository->findOneBy(['email'=>$donnees]);
            if(!$user) {
                $this->addFlash('danger','cette adresse n\'existe pas');
                return $this->redirectToRoute("forgot");

            }
            $token = $tokenGenerator->generateToken();

            try{
                $user->setResetToken($token);
                $entityManger = $this->getDoctrine()->getManager();
                $entityManger->persist($user);
                $entityManger->flush();




            }catch(\Exception $exception) {
                $this->addFlash('warning','une erreur est survenue :'.$exception->getMessage());
                return $this->redirectToRoute("app_login");


            }

            $url = $this->generateUrl('app_reset_password',array('token'=>$token),UrlGeneratorInterface::ABSOLUTE_URL);
        $email = (new Email())
        ->from('rayan.lahmar@esprit.tn')
        ->To($user->getEmail())
        ->subject('Rénitialisation de mot de passe')
                ->text("<p> Bonjour</p> unde demande de réinitialisation de mot de passe a été effectuée. Veuillez cliquer sur le lien suivant :".$url,
                "text/html");
        // ->text('<p> Bonjour</p> unde demande de réinitialisation de mot de passe a été effectuée. Veuillez cliquer sur le lien suivant :".$url,
        // "text/html');
       
 try {
        $mailer->send($email);
        $this->addFlash('message','E-mail  de réinitialisation du mp envoyé :');
    } catch (TransportExceptionInterface $e) {
        // Gérer les erreurs d'envoi de courriel
    }

}
return $this->render("User/forgotPassword.html.twig",['form'=>$form->createView()]);


    }

    /**
     * @Route("/resetpassword/{token}", name="app_reset_password")
     */
    public function resetpassword(Request $request,string $token, UserPasswordEncoderInterface  $passwordEncoder)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['reset_token'=>$token]);

        if($user == null ) {
            $this->addFlash('danger','TOKEN INCONNU');
            return $this->redirectToRoute("app_login");

        }

        if($request->isMethod('POST')) {
            $user->setResetToken(null);

            $user->setPassword($passwordEncoder->encodePassword($user,$request->request->get('password')));
            $entityManger = $this->getDoctrine()->getManager();
            $entityManger->persist($user);
            $entityManger->flush();

            $this->addFlash('message','Mot de passe mis à jour :');
            return $this->redirectToRoute("app_login");

        }
        else {
            return $this->render("user/resetPassword.html.twig",['token'=>$token]);

        }
    }


}
