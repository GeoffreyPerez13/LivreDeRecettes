<?php

namespace App\Form;

use App\Entity\Recipe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Event\PostSubmitEvent;
use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Sequentially;

class RecipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre :',
                'empty_data' => ''
            ])
            ->add('slug', TextType::class, [
                'required' => false,
                'label' => 'Slug :'
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Contenu :',
                'empty_data' => ''
            ])
            ->add('duration', TextType::class, [
                'label' => 'Temps de préparation :'
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Envoyer'
            ])
            ->addEventListener(FormEvents::PRE_SUBMIT, [$this, 'autoSlug'])
            ->addEventListener(FormEvents::POST_SUBMIT, [$this, 'attachTimeStamps']);
    }

    // Créer un slug automatique si l'utilisateur ne le complète pas dans le formulaire
    public function autoSlug(PreSubmitEvent $event): void
    {
        // Récupère les données du formulaire
        $data =  $event->getData();

        // Vérifie si le champ "slug" est vide dans les données soumises
        if (empty($data['slug'])) {
            // Instancie le composant Slugger pour générer un slug ASCII à partir du titre
            $slugger = new AsciiSlugger();

            // Génère un slug en utilisant le titre, puis le convertit en minuscules
            $data['slug'] = strtolower($slugger->slug($data['title']));

            // Remplace les données originales du formulaire avec les nouvelles données 
            $event->setData($data);
        }
    }

    // Ajouter des timestamps à la soumission du formulaire
    public function attachTimeStamps(PostSubmitEvent $event): void
    {
        $data =  $event->getData();

        // Si les données ne sont pas une instance de la classe Recipe, on arrête l'exécution
        if (!($data instanceof Recipe)) {
            return;  // On sort de la fonction si l'objet n'est pas une recette
        }

        // Si l'objet est une recette, on met à jour le champ 'updatedAt' avec la date et heure actuelles
        $data->setUpdatedAt(new \DateTimeImmutable());

        // Si l'ID de la recette n'existe pas (c'est-à-dire si c'est une nouvelle recette),
        // on initialise également le champ 'createdAt' avec la date et heure actuelles
        if (!$data->getId()) {
            $data->setCreatedAt(new \DateTimeImmutable());
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class
        ]);
    }
}
