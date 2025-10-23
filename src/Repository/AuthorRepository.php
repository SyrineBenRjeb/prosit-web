<?php

namespace App\Repository;

use App\Entity\Author;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Author>
 */
class AuthorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Author::class);
    }

    public function listAuthorByEmail(): array
{
    return $this->createQueryBuilder('a')
        ->orderBy('a.email', 'ASC') // Tri alphabétique par adresse email
        ->getQuery()
        ->getResult();
}


//    /**
//     * @return Author[] Returns an array of Author objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Author
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
 public function ShowAllAuthorsDQL(): mixed
{
    $query = $this->getEntityManager()
        ->createQuery('
            SELECT a
            FROM App\Entity\Author a
            WHERE a.username LIKE :condition
            ORDER BY a.username ASC
        ')
        ->setParameter('condition', '%a%');

    return $query->getResult();
}
public function findAuthorsByNbBooksRange(int $min, int $max): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.nbBooks BETWEEN :min AND :max')
            ->setParameter('min', $min)
            ->setParameter('max', $max)
            ->orderBy('a.username', 'ASC')
            ->getQuery()
            ->getResult();
    }

}