<?php

namespace App\Form;

use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvents;

class CategoryType extends AbstractType
{
    // Le service FormListenerFactory est injecté ici (pour ajouter des événements personnalisés au formulaire)
    public function __construct(private FormListenerFactory $listenerFactory) {}

    // Cette méthode construit le formulaire
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Champ "name" (obligatoire)
            ->add('name', TextType::class, [
                'label' => 'Nom :', // Étiquette affichée dans le formulaire
                'empty_data' => '' // Valeur par défaut si aucun texte n’est saisi
            ])
            // Champ "slug" (optionnel)
            ->add('slug', TextType::class, [
                'required' => false, // Ce champ peut être laissé vide
                'empty_data' => '', // Valeur par défaut si vide
                'label' => 'Slug :'
            ])
            // Bouton de soumission
            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer' // Texte affiché sur le bouton
            ])
            // Événement exécuté avant la soumission : génère un slug automatiquement à partir du nom si le champ est vide
            ->addEventListener(FormEvents::PRE_SUBMIT, $this->listenerFactory->autoSlug('name'))
            // Événement exécuté après soumission : ajoute les timestamps (createdAt / updatedAt)
            ->addEventListener(FormEvents::POST_SUBMIT, $this->listenerFactory->timestamps());
    }

    // Configure les options par défaut du formulaire
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Category::class,  // Ce formulaire manipule des entités de type Category
        ]);
    }
}