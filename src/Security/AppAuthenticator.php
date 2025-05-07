<?php

namespace App\Security;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class AppAuthenticator extends AbstractLoginFormAuthenticator
{
    // Permet de gérer le retour à la page précédente après connexion
    use TargetPathTrait;

    // Nom de la route de connexion
    public const LOGIN_ROUTE = 'app_login';

    // Injection du générateur d'URL et du UserRepository
    public function __construct(private UrlGeneratorInterface $urlGenerator, private UserRepository $userRepository) {}

    /**
     * Cette méthode est appelée pour authentifier l'utilisateur à partir des données du formulaire de connexion.
     */
    public function authenticate(Request $request): Passport
    {
        // On récupère le champ 'username' du formulaire (ça peut être l'email ou le pseudo)
        $username = $request->getPayload()->getString('username');

        // On le stocke en session pour le préremplir si besoin
        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $username);

        // Création d'un "Passport" avec les infos nécessaires à l'authentification
        return new Passport(
            new UserBadge($username, fn(string $identifier) => $this->userRepository->findUserByEmailOrUsername($identifier)),
            new PasswordCredentials($request->getPayload()->getString('password')),
            [
                new CsrfTokenBadge('authenticate', $request->getPayload()->getString('_csrf_token')),
                new RememberMeBadge(),
            ]
        );
    }

    /**
     * Méthode appelée après une authentification réussie.
     * Elle permet de rediriger l'utilisateur selon son rôle.
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // On récupère l'objet User authentifié
        $user = $token->getUser();

        // Si l'utilisateur a le rôle ADMIN, on le redirige vers la page d'admin
        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            return new RedirectResponse($this->urlGenerator->generate('admin.recipe.index'));
        }

        // Sinon, on le redirige vers la page d'accueil
        return new RedirectResponse($this->urlGenerator->generate('home'));
    }

    /**
     * Méthode appelée pour obtenir l'URL de la page de login.
     */
    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}