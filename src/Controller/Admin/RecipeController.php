<?php

namespace App\Controller\Admin;

use App\Demo;
use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route("admin/recettes", name: 'admin.recipe.')]
class RecipeController extends AbstractController
{
    // Route pour afficher toutes les recettes
    #[Route('/', name: 'index')]
    public function index(RecipeRepository $repository): Response
    {
        // On récupère toutes les recettes depuis le repository
        // $recipes = $repository->findWithDurationLowerThan(30);
        $recipes = $repository->findAll();

        // On renvoie la réponse en affichant la vue
        return $this->render('admin/recipe/index.html.twig', [
            'recipes' => $recipes
        ]);
    }

    // Route pour créer une recette
    #[Route('/create', name: 'create')]
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
            return $this->redirectToRoute('admin.recipe.index');
        }

        return $this->render('admin/recipe/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    // Route pour mettre à jour une recette
    #[Route('/{id}', name: 'edit', methods: ['GET', 'POST'], requirements: ['id' => Requirement::DIGITS])]
    public function edit(Recipe $recipe, Request $request, EntityManagerInterface $em, FormFactoryInterface $formFactory)
    {

        // Création du formulaire d'édition de recette
        $form = $formFactory->create(RecipeType::class, $recipe);

        // Gérer la requête du formulaire
        $form->handleRequest($request);

        // Si le formulaire est soumit et valide
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush(); // Applique les modification à l'entité et enregistre le tout en BDD

            $this->addFlash('success', 'La recette a bien été modifiée.');
            return $this->redirectToRoute('admin.recipe.index');
        }

        return $this->render('admin/recipe/edit.html.twig', [
            'recipe' => $recipe,
            'form' => $form->createView()  // Ne pas oublier de passer la vue du formulaire
        ]);
    }

    // Route pour supprimer
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function remove(Recipe $recipe, EntityManagerInterface $em)
    {
        $em->remove($recipe);
        $em->flush();

        $this->addFlash('success', 'La recette a bien été supprimée.');

        return $this->redirectToRoute('admin.recipe.index');
    }
}
