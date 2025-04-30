<?php

namespace App\Controller;

use App\DTO\ContactDTO;
use App\Form\ContactType;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Attribute\Route;

final class ContactController extends AbstractController
{
    // Définition de la route "/contact" avec le nom "contact"
    #[Route('/contact', name: 'contact')]
    public function contact(Request $request, MailerInterface $mailer): Response
    {
        // Instanciation d'un objet ContactDTO (données initiales du formulaire)
        $data = new ContactDTO();

        // Création du formulaire basé sur ContactType, lié à l'objet $data
        $form = $this->createForm(ContactType::class, $data);
        $form->handleRequest($request); // Hydrate $data avec les données POST si formulaire soumis

        // Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                // Création d'un e-mail basé sur un template Twig
                $email = (new TemplatedEmail())
                    ->to($data->service) // Adresse du destinataire choisie dans le champ "service"
                    ->from($data->email) // Adresse de l’expéditeur saisie dans le formulaire
                    ->subject('Demande de contact') // Sujet de l’e-mail
                    ->htmlTemplate('emails/contact.html.twig') // Template Twig utilisé pour le contenu HTML
                    ->context(['data' => $data]); // Passage des données au template (ex. nom, message, etc.)

                // Envoi de l’e-mail
                $mailer->send($email);

                // Message flash de succès à afficher après redirection
                $this->addFlash('success', 'Votre email a bien été envoyé');

                // Redirection vers la page de contact (permet de recharger la page vide)
                return $this->redirectToRoute('contact');
            } catch (\Exception $exception) {
                // En cas d’erreur lors de l’envoi du mail, afficher un message d’erreur
                $this->addFlash('danger', 'Impossible d\'envoyer votre email');
            }
        }

        // Affichage de la page de contact avec le formulaire
        return $this->render('contact/contact.html.twig', [
            'form' => $form,
        ]);
    }
}
