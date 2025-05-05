<?php

namespace App\Form;

use Symfony\Component\Form\Event\PostSubmitEvent;
use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\String\Slugger\SluggerInterface;

class FormListenerFactory
{
    public function __construct(private SluggerInterface $slugger){

    }

    public function autoSlug(string $field): callable
    {
        return function (PreSubmitEvent $event) use ($field) {
            // Récupère les données du formulaire
            $data =  $event->getData();

            // Vérifie si le champ "slug" est vide dans les données soumises
            if (empty($data['slug'])) {
                // Génère un slug en utilisant le titre, puis le convertit en minuscules
                $data['slug'] = strtolower($this->slugger->slug($data[$field]));

                // Remplace les données originales du formulaire avec les nouvelles données 
                $event->setData($data);
            }
        };
    }

    public function timestamps(): callable
    {
        return function (PostSubmitEvent $event){
            $data =  $event->getData();

            // Si l'objet est une recette, on met à jour le champ 'updatedAt' avec la date et heure actuelles
            $data->setUpdatedAt(new \DateTimeImmutable());

            // Si l'ID de la recette n'existe pas (c'est-à-dire si c'est une nouvelle recette),
            // on initialise également le champ 'createdAt' avec la date et heure actuelles
            if (!$data->getId()) {
                $data->setCreatedAt(new \DateTimeImmutable());
            }
        };
    }
}
