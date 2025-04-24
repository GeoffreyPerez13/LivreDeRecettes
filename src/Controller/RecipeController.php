<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RecipeController extends AbstractController
{
    // Route pour afficher toutes les recettes
    #[Route('/recettes', name: 'recipe.index')]
    public function index(Request $request, RecipeRepository $repository): Response
    {
        // On récupère toutes les recettes depuis le repository
        // $recipes = $repository->findWithDurationLowerThan(30);
        $recipes = $repository->findAll();

        // On renvoie la réponse en affichant la vue
        return $this->render('recipe/index.html.twig', [
            'recipes' => $recipes
        ]);
    }

    // Route pour afficher une recette spécifique
    #[Route('/recettes/{slug}-{id}', name: 'recipe.show', requirements: ['id' => '\d+', 'slug' => '[a-z0-9-]+'])]
    public function show(Request $request, string $slug, int $id, RecipeRepository $repository): Response
    {
        // On récupère la recette à partir de l'id
        $recipe = $repository->find($id);

        // Si la recette existe, mais que le slug dans l'URL ne correspond pas au slug de la recette,
        if ($recipe->getSlug() != $slug) {
            // on redirige l'utilisateur vers l'URL correcte avec le bon slug.
            return $this->redirectToRoute('recipe.show', [
                'slug' => $recipe->getSlug(), // On passe le slug correct
                'id' => $recipe->getId() // On passe l'id de la recette
            ]);
        }

        // Si le slug est correct, on affiche la recette en question dans la vue
        return $this->render('recipe/show.html.twig', [
            'recipe' => $recipe  // On passe l'objet 'recipe' à la vue
        ]);
    }

    // Route pour mettre à jour une recette
    #[Route('/recettes/{id}/edit', name: 'recipe.edit', methods: ['GET', 'POST'])]
    public function edit(Recipe $recipe, Request $request, EntityManagerInterface $em)
    {

        // Création du formulaire d'édition de recette
        $form = $this->createForm(RecipeType::class, $recipe);

        // Gérer la requête du formulaire
        $form->handleRequest($request);

        // Si le formulaire est soumit et valide
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush(); // Applique les modification à l'entité et enregistre le tout en BDD

            $this->addFlash('success', 'La recette a bien été modifiée.');
            return $this->redirectToRoute('recipe.index');
        }

        return $this->render('recipe/edit.html.twig', [
            'recipe' => $recipe,
            'form' => $form->createView()  // Ne pas oublier de passer la vue du formulaire
        ]);
    }

    // Route pour créer une recette
    #[Route('/recettes/create', name: 'recipe.create')]
    public function create(Request $request, EntityManagerInterface $em)
    {
        // Création d'une nouvelle recette
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($recipe); // Persiter les données
            $em->flush(); // Applique les modification à l'entité et enregistre le tout en BDD

            $this->addFlash('success', 'La recette a bien été créée.');
            return $this->redirectToRoute('recipe.index');
        }

        return $this->render('recipe/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    // Route pour supprimer
    #[Route('/recettes/{id}', name: 'recipe.delete', methods: ['DELETE'])]
    public function remove(Recipe $recipe, EntityManagerInterface $em) {
        $em->remove($recipe);
        $em->flush();

        $this->addFlash('success','La recette a bien été supprimée.');

        return $this->redirectToRoute('recipe.index');
    }
}
