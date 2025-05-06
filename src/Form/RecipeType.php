<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Recipe;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Event\PostSubmitEvent;
use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Sequentially;

class RecipeType extends AbstractType
{
    // Injection du service FormListenerFactory, qui permet d'ajouter des comportements personnalisés
    public function __construct(private FormListenerFactory $listenerFactory) {}

    // Méthode principale pour construire les champs du formulaire
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Champ "title" (obligatoire)
            ->add('title', TextType::class, [
                'label' => 'Titre :', // Libellé affiché dans le formulaire
                'empty_data' => '' // Valeur utilisée si l'utilisateur laisse ce champ vide
            ])

            // Champ "slug" (optionnel, généré automatiquement si vide)
            ->add('slug', TextType::class, [
                'required' => false, // Le champ n'est pas obligatoire
                'label' => 'Slug :'
            ])

            ->add('thumbnailFile', FileType::class, [
                'label' => 'Image :'
            ])

            ->add('category', EntityType::class, [
                'label' => 'Catégorie :',
                'class' => Category::class,
                'expanded' => true,
                'choice_label' => 'name'
            ])

            // Champ "content" (textarea pour texte long)
            ->add('content', TextareaType::class, [
                'label' => 'Contenu :', // Libellé du champ
                'empty_data' => '' // Définit une valeur vide si aucun contenu
            ])

            // Champ "duration" (temps de préparation)
            ->add('duration', TextType::class, [
                'label' => 'Temps de préparation :'
            ])

            // Bouton de soumission
            ->add('save', SubmitType::class, [
                'label' => 'Envoyer'
            ])

            // Ajoute un écouteur pour générer automatiquement le slug à partir du titre si celui-ci est vide
            ->addEventListener(FormEvents::PRE_SUBMIT, $this->listenerFactory->autoSlug('title'))

            // Ajoute un écouteur pour définir ou mettre à jour les dates createdAt / updatedAt
            ->addEventListener(FormEvents::POST_SUBMIT, $this->listenerFactory->timestamps());
    }

    // Définit les options par défaut pour ce formulaire
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class   // Le formulaire est lié à l'entité Recipe
        ]);
    }
}