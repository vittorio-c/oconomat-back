<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;



class SecurityController extends AbstractController
{


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

        //dump($emailList);

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
}
