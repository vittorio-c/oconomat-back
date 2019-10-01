<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils, Security $security, Request $request): Response
    {
        // if ($this->getUser()) {
        //    $this->redirectToRoute('target_path');
        // }
        //$user = $this->getUser();
        //dump($security->getUser());

        // get the login error if there is one
        //$error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        //$lastUsername = $authenticationUtils->getLastUsername();
        // if($user == null){
        //     return $this->json("Les identifiants sont incorrects");
        //     exit;
        // }
        //return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
        // return $this->json([
        //     'success' => "Authentification de l'utilisateur avec succès",
        //     "email" => $user->getEmail()
        // ]);

        $user = $this->getUser();

        return $this->json([
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
        ]);
    }

    /**
     * @Route("/logout", name="app_logout")
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

    }
}
