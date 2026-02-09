<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

final class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Redirige si deja connecte
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        // Recupere l'erreur de login s'il y en a une
        $error = $authenticationUtils->getLastAuthenticationError();

        // Dernier identifiant saisi par l'utilisateur
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        // Cette methode est interceptee par le firewall logout
        // Elle ne sera jamais executee
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
