<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route("admin/category", name: 'admin.category.')]
#[IsGranted('ROLE_ADMIN')]
class CategoryController extends AbstractController
{
    // Affiche la liste de toutes les catégories
    #[Route(name: 'index')]
    public function index(CategoryRepository $repository) {
        // Récupère toutes les catégories depuis la base de données
        $categories = $repository->findAll();

        // Affiche la vue avec la liste des catégories
        return $this->render('admin/category/index.html.twig', [
            'categories' => $categories
        ]);
    }

    // Permet de créer une nouvelle catégorie
    #[Route('/create', name: 'create')]
    public function create(Request $request, EntityManagerInterface $em) {
        // Crée une instance vide de catégorie
        $category = new Category();

        // Crée le formulaire à partir de la classe CategoryType
        $form = $this->createForm(CategoryType::class, $category);

        // Gère la requête HTTP et hydrate l'objet $category
        $form->handleRequest($request);

        // Vérifie si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Persiste (prépare) la catégorie pour la sauvegarde
            $em->persist($category);

            // Exécute l'enregistrement en base de données
            $em->flush();

            // Message flash de confirmation
            $this->addFlash('success', 'La catégorie a bien été créée');

            // Redirige vers la liste des catégories
            return $this->redirectToRoute('admin.category.index');
        }

        // Affiche le formulaire s’il n’a pas encore été soumis ou s’il contient des erreurs
        return $this->render('admin/category/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    // Permet d’éditer une catégorie existante (GET pour afficher le formulaire, POST pour soumettre)
    #[Route('/{id}', name: 'edit', requirements: ['id' => Requirement::DIGITS], methods: ['GET', 'POST'])]
    public function edit(Category $category, Request $request, EntityManagerInterface $em) {
        // Crée le formulaire avec la catégorie existante (hydrate les champs)
        $form = $this->createForm(CategoryType::class, $category);

        // Traite la requête (mise à jour des données avec les valeurs envoyées)
        $form->handleRequest($request);

        // Si le formulaire est valide, sauvegarde les modifications
        if ($form->isSubmitted() && $form->isValid()) {
            // Sauvegarde des modifications (pas besoin de persist() car l'objet est déjà connu)
            $em->flush();

            // Message de succès
            $this->addFlash('success', 'La catégorie a bien été modifiée');

            // Redirige vers la liste des catégories
            return $this->redirectToRoute('admin.category.index');
        }

        // Affiche le formulaire avec les données existantes
        return $this->render('admin/category/edit.html.twig', [
            'category' => $category,
            'form' => $form->createView()
        ]);
    }

    // Supprime une catégorie existante
    #[Route('/{id}', name: 'delete', requirements: ['id' => Requirement::DIGITS], methods: ['DELETE'])]
    #[Route(name: 'remove')]
    public function remove(Category $category, EntityManagerInterface $em) {
        // Supprime l'entité
        $em->remove($category);

        // Exécute la suppression en base de données
        $em->flush();

        // Message de confirmation
        $this->addFlash('success', 'La catégorie a bien été supprimée');

        // Redirige vers la liste des catégories
        return $this->redirectToRoute('admin.category.index');
    }
}