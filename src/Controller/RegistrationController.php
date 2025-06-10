<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\AppAuthenticator;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    // Constructeur qui injecte le service EmailVerifier pour gérer la vérification d'email
    public function __construct(private EmailVerifier $emailVerifier)
    {
    }

    // Route pour l'enregistrement des utilisateurs
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager): Response
    {
        // Création d'un nouvel utilisateur
        $user = new User();

        // Création du formulaire d'enregistrement en utilisant un formulaire personnalisé
        $form = $this->createForm(RegistrationFormType::class, $user);

        // Traitement des données envoyées par le formulaire
        $form->handleRequest($request);

        // Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            // Récupération du mot de passe en texte brut
            $plainPassword = $form->get('plainPassword')->getData();

            // Hachage du mot de passe avant de l'enregistrer dans la base de données
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            // Attribuer le rôle "ROLE_USER" à l'utilisateur
            $user->setRoles(['ROLE_USER']);

            // Sauvegarde de l'utilisateur dans la base de données
            $entityManager->persist($user);
            $entityManager->flush();

            // Envoi d'un email de confirmation pour la vérification de l'email de l'utilisateur
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('support@demo.fr', 'Support')) // L'adresse de l'expéditeur
                    ->to((string) $user->getEmail()) // L'adresse de l'utilisateur
                    ->subject('Veuillez confirmer votre adresse mail') // Objet de l'email
                    ->htmlTemplate('registration/confirmation_email.html.twig') // Template du message HTML
            );

            // Connexion de l'utilisateur après l'enregistrement
            return $security->login($user, AppAuthenticator::class, 'main');
        }

        // Si le formulaire n'est pas soumis ou n'est pas valide, on renvoie le formulaire d'inscription
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form, // On passe le formulaire pour l'affichage
        ]);
    }

    // Route pour la vérification de l'email
    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator): Response
    {
        // On s'assure que l'utilisateur est bien connecté
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // Validation du lien de confirmation d'email, il met à jour User::isVerified et persiste
        try {
            /** @var User $user */
            // Récupération de l'utilisateur connecté
            $user = $this->getUser();

            // Gestion de la confirmation de l'email
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            // Si une exception est levée lors de la vérification, on affiche un message d'erreur
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            // Redirection vers la page d'enregistrement en cas d'erreur
            return $this->redirectToRoute('app_register');
        }

        // Message de succès si l'email a bien été vérifié
        $this->addFlash('success', 'Votre adresse mail a bien été vérifiée.');

        // Redirection vers la page de connexion après la vérification de l'email
        return $this->redirectToRoute('app_login');
    }
}