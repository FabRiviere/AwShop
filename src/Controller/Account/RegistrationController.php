<?php

namespace App\Controller\Account;

use App\Entity\Users;
use App\Form\RegistrationFormType;
use App\Repository\UsersRepository;
use App\Security\UsersAuthenticator;
use App\Service\JWTService;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher,
                             UserAuthenticatorInterface $userAuthenticator, UsersAuthenticator $authenticator,
                              EntityManagerInterface $entityManager, SendMailService $mail, JWTService $jwt): Response
    {
        $user = new Users();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            // On génère le JWT de l'utilisateur
            // ; On crée le $header
            $header = [
                'typ' => 'JWT',
                'alg' => 'HS256'
            ];
            // ; On crée le payload
            $payload = [
                'user_id' => $user->getId()
            ];
            // ; On génère le token
            $token = $jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'));

            
            // Envoie d'un mail
            $mail->send(
                'no-reply@awshop.fr',
                $user->getEmail(),
                'Activation de votre compte sur le site e-commerce AwShop',
                'register',
                compact('user', 'token')
            );

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    // Création d'une nouvelle route pour vérifier le token de l'utilisateur
    #[Route('/verif/{token}', name: 'app_verify_user')]
    public function verifyUser($token, JWTService $jwt, UsersRepository $usersRepository, EntityManagerInterface $manager): Response
    {
        // On vérifie si token est valide, n'a pas expiré et pas modifié
        if ($jwt->isValid($token) && !$jwt->isExpired($token) && $jwt->check($token, $this->getParameter('app.jwtsecret'))) {
            
            //; On récupère le payload
            $payload = $jwt->getPayload($token);
            
            //; On récupère le user du token
            $user = $usersRepository->find($payload['user_id']);

            //; On vérifie que l'utilisateur existe et n'a pas encore activé son compte
            if ($user && !$user->isIsVerified()) {
                $user->setIsVerified(true);
                $manager->flush($user);
                $this->addFlash('token_message_success', 'Utilisateur activé ! 👍');
                return $this->redirectToRoute('app_home');
            } 
        }
        // Ici un problème se pose ds le token
        $this->addFlash('token_message_error', 'Le token est invalide ou à expiré 😟');

        return $this->redirectToRoute('app_login');
    }

    // Méthode pour renvoyer le lien d'activation si token expiré ou mail supprimé, etc.
    #[Route('/renvoiVerif', name: 'app_resend_verif')]
    public function resendVerif(JWTService $jwt, SendMailService $mail, UsersRepository $usersRepository): Response
    {
        $user = $this->getUser();

        if(!$user) {
            $this->addFlash('resend_verif_error', 'Vous devez être connecté pour accéder à cette page');
            return $this->redirectToRoute('app_login');
        }

        if ($user->isIsVerified()) {
            $this->addFlash('resend_verif_warning', 'Cet utilisateur est déjà activé');
            return $this->redirectToRoute('app_home');
        }

        // On reproduit l'envoi d'un token et d'un email
        $header = [
            'typ' => 'JWT',
            'alg' => 'HS256'
        ];
        $payload = [
            'user_id' => $user->getId()
        ];
       
        $token = $jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'));

        $mail->send(
            'no-reply@awshop.fr',
            $user->getEmail(),
            'Activation de votre compte sur le site e-commerce AwShop',
            'register',
            compact('user', 'token')
        );
        $this->addFlash('resend_verif_success', 'Email de vérification envoyé');
            return $this->redirectToRoute('app_home');
    }
}
