<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    /**
     * Recherche un utilisateur par e-mail ou nom d'utilisateur.
     *
     * @param string $userNameOrEmail L'identifiant fourni par l'utilisateur (e-mail ou nom d'utilisateur)
     * @return User|null Retourne l'utilisateur correspondant, ou null si aucun trouvé
     */
    public function findUserByEmailOrUsername(string $userNameOrEmail): ?User
    {
        return $this->createQueryBuilder('u') // Initialise le constructeur de requête pour l'entité User (alias 'u')
            ->where('u.email = :identifier OR u.username = :identifier') // Cherche un utilisateur avec cet e-mail
            ->setParameter('identifier', $userNameOrEmail) // Définit la valeur du paramètre :identifier
            ->setMaxResults(1) // Limite à un seul résultat
            ->getQuery() // Prépare la requête Doctrine
            ->getOneOrNullResult(); // Exécute et retourne un User ou null si aucun trouvé
    }

    //    /**
    //     * @return User[] Returns an array of User objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?User
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
