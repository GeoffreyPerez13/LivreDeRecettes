<?php

namespace App\Form;

use App\DTO\ContactDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    // Construction du formulaire
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Champ pour le nom de l'utilisateur
            ->add('name', TextType::class, [
                'empty_data' => '' // Définit une valeur vide par défaut si aucun input n'est donné
            ])

            // Champ pour l'email avec validation automatique d'un format email
            ->add('email', EmailType::class, [
                'empty_data' => ''
            ])

            // Zone de texte pour le message
            ->add('message', TextareaType::class, [
                'empty_data' => ''
            ])

            // Bouton de soumission
            ->add('save', SubmitType::class, [
                'label' => 'Envoyer' // Libellé du bouton
            ])

            // Liste déroulante pour choisir un service à contacter
            ->add('service', ChoiceType::class, [
                'choices'  => [ // Clé = libellé affiché, valeur = donnée envoyée
                    'Compta' => 'compta@demo.fr',
                    'Support' => 'support@demo.fr',
                    'Marketing' => 'marketing@demo.fr',
                ],
            ]);
    }

    // Lien entre ce formulaire et le DTO ContactDTO, qui sert de modèle de données temporaire
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ContactDTO::class,
        ]);
    }
}