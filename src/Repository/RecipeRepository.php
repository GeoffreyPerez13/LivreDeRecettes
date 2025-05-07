<?php

namespace App\Repository;

use App\Entity\Recipe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends ServiceEntityRepository<Recipe>
 */
class RecipeRepository extends ServiceEntityRepository
{
    // Le constructeur injecte le ManagerRegistry, qui permet de gérer les entités avec Doctrine
    public function __construct(ManagerRegistry $registry, private PaginatorInterface $paginator)
    {
        // Appel au constructeur parent de ServiceEntityRepository en passant le registre et la classe de l'entité Recipe
        parent::__construct($registry, Recipe::class);
    }

    // Cette méthode permet de paginer les recettes récupérées
    public function paginateRecipes(int $page): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->createQueryBuilder('r')->leftJoin('r.category', 'c')->select('r', 'c'),
            $page,
            20,
            [
                'distinct' => true,
                'sortFieldAllowList' => ['r.id', 'r.title']
            ]
        );
    }

    // // Création d'une requête avec un QueryBuilder pour récupérer toutes les recettes
    // // La pagination est effectuée en utilisant Paginator
    // return new Paginator($this
    // ->createQueryBuilder('r')
    // ->setFirstResult(($page - 1) * $limit)
    // ->setMaxResults($limit)
    // ->getQuery()
    // ->setHint(Paginator::HINT_ENABLE_DISTINCT, false),
    // false
    // );


    // Cette méthode calcule la durée totale de toutes les recettes
    public function findTotalDuration(): int
    {
        // On crée une requête qui sélectionne la somme des durées des recettes
        return $this->createQueryBuilder('r')
            ->select('SUM(r.duration) as total')  // On sélectionne la somme de la colonne 'duration'
            ->getQuery() // Exécution de la requête
            ->getSingleScalarResult(); // Récupère le résultat sous forme d'un seul scalaire
    }

    /**
     * Trouver des recettes avec un temps de cuisine inférieur à une certaine durée
     * @param int $duration
     * @return Recipe[] // Renvoie un tableau de recettes
     */
    public function findWithDurationLowerThan(int $duration): array
    {
        // Création d'une requête qui cherche des recettes avec une durée de cuisine inférieure ou égale à la valeur donnée
        return $this->createQueryBuilder('r')
            ->where('r.duration <= :duration')  // Condition de filtre sur la durée
            ->orderBy('r.duration', 'ASC')  // On trie les résultats par durée croissante
            ->setMaxResults(10)  // On limite le nombre de résultats à 10
            ->setParameter('duration', $duration)  // On définit le paramètre de la requête (la durée)
            ->getQuery()  // Exécution de la requête
            ->getResult(); // On récupère les résultats sous forme de tableau d'objets Recipe
    }
}
