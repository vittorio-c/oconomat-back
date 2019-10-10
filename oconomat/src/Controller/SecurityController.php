<?php

namespace App\Controller;

use App\Entity\User;
use Swift_Mailer;
use App\Service\getRandomPassword;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;



class SecurityController extends AbstractController
{

    private $mailer;

    public function __construct(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }
    /**
     * @Route("/api/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }

    /**
     * @Route("/api/register", name="app_register", methods={"POST"})
     */
    public function register(Request $request, UserPasswordEncoderInterface $encoder)
    {

        $user  = new User();

        // Création d'un nouvel utilisateur.
        // Récupération des données envoyées depuis le front-end.

        $firstName = $request->request->get('firstname');
        $lastName = $request->request->get('lastname');
        $email = $request->request->get('email');
        $clearPassword = $request->request->get('password');
        $confirmPassword = $request->request->get('passwordConfirm');

        $emailList = $this->getDoctrine()->getRepository(User::class)->getEmailList();


        foreach($emailList as $emailArray){
            foreach($emailArray as $value){
                if($email == $value){
                    return $this->json('Email déjà existante');
                }
            }
        }

        if($clearPassword == $confirmPassword){
            
            $encodedPassword = $encoder->encodePassword($user, $clearPassword);

            $user->setFirstname($firstName);
            $user->setLastname($lastName);
            $user->setEmail($email);
            $user->setRoles(['ROLE_USER']);
            $user->setPassword($encodedPassword);
    
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
    
            return $this->json('Utilisateur ajouté en base de données !');
        }else{
            return $this->json('Les mots de passe ne correspondent pas !');
        }

    }


    /**
     * @Route("/api/password/new", name="app_password_new", methods={"POST"})
     */
    public function forgotPassword(Request $request, getRandomPassword $randomPassword, \Swift_Mailer $mailer, UserPasswordEncoderInterface $encoder)
    {

        // Vérifier l'utilisateur en cherchant son email dans la base de données
        $email = $request->request->get('email');
        $emailList = $this->getDoctrine()->getRepository(User::class)->getEmailList();

        foreach($emailList as $emailArray){
            foreach($emailArray as $value){
                if($email == $value){

                    // Trouver l'id utilisateur en fonction de l'email trouvée en bdd
                    $userIdByEmail = $this->getDoctrine()->getRepository(User::class)->findUserIdByEmail($email); 
                    $userId =  $userIdByEmail[0]['id'];

                    $currentUser = $this->getDoctrine()->getRepository(User::class)->find($userId);

                    // Créer un nouveau mot de passe

                    $newPasswordGenerated = $randomPassword->generateRandomString();
                    
                    // L'envoyer via email de l'utilisateur 

                    $message = (new \Swift_Message('Votre nouveau mot de passe'))
                    ->setFrom($email)
                    ->setTo($email)
                    ->setBody(
                        $this->renderView(
                            // templates/emails/registration.html.twig
                            'emails/newpassword.html.twig',
                            ['password' => $newPasswordGenerated]
                        ),
                        'text/html'
                    )
                    ;
            
                    $mailer->send($message);

                    // Encoder ce même mot de passe

                    $encodedPassword = $encoder->encodePassword($currentUser, $newPasswordGenerated);
                    $currentUser->setPassword($encodedPassword);

                    // le persister dans la base de données

                    $em = $this->getDoctrine()->getManager();
                    $em->persist($currentUser);
                    $em->flush();

                   
                    return $this->json('Email avec votre nouveau mot de passe envoyé à'.$email);
                }
            }
        }
        
    }


}
