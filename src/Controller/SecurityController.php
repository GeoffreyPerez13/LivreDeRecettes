<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    // Route pour afficher le formulaire de connexion
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Récupération de l'erreur d'authentification (si elle existe)
        $error = $authenticationUtils->getLastAuthenticationError();

        // Récupération du dernier nom d'utilisateur saisi (si une tentative de connexion précédente a échoué)
        $lastUsername = $authenticationUtils->getLastUsername();

        // Rendu de la vue de connexion avec les informations d'erreur et le dernier nom d'utilisateur
        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,  // On passe le dernier nom d'utilisateur pour le pré-remplissage du champ
            'error' => $error,  // On passe l'erreur d'authentification s'il y en a une
        ]);
    }

    // Route pour la déconnexion (cette méthode peut rester vide, la déconnexion étant gérée automatiquement par Symfony)
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        // Cette méthode ne fait rien car elle est interceptée par la configuration de Symfony et le logout est géré automatiquement
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}