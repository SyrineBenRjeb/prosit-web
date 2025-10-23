<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Book>
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    /**
     * 🔍 Recherche des livres par référence partielle.
     */
    public function searchBookByRef(string $ref): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.ref LIKE :ref')
            ->setParameter('ref', '%' . $ref . '%')
            ->orderBy('b.ref', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * 📚 Liste des livres triés par auteur puis par titre.
     */
    public function booksListByAuthors(): array
    {
        return $this->createQueryBuilder('b')
            ->leftJoin('b.author', 'a')
            ->addSelect('a')
            ->orderBy('a.username', 'ASC')
            ->addOrderBy('b.title', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * 🔒 S'assure que l'année est comprise entre 2010 et 2030
     */
    private function sanitizeYear(int $year): int
    {
        if ($year < 2010) {
            return 2010;
        }
        if ($year > 2030) {
            return 2030;
        }
        return $year;
    }

    /**
     * 🕓 Trouve les livres publiés avant une certaine année
     * et dont l'auteur a au moins un certain nombre de livres.
     */
    public function findBooksBeforeYearWithAuthorMinBooks(int $year = 2023, int $minBooks = 10): array
    {
        $year = $this->sanitizeYear($year);

        return $this->createQueryBuilder('b')
            ->leftJoin('b.author', 'a')
            ->addSelect('a')
            ->where('b.publicationDate < :date')
            ->andWhere('a.nbBooks >= :minBooks')
            ->andWhere('b.published = :published')
            ->setParameter('date', new \DateTime("$year-01-01"))
            ->setParameter('minBooks', $minBooks)
            ->setParameter('published', true)
            ->orderBy('b.publicationDate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * ✏️ Met à jour la catégorie des livres d'une catégorie vers une autre.
     */
    public function updateCategoryFromTo(string $fromCategory, string $toCategory): int
    {
        return $this->createQueryBuilder('b')
            ->update()
            ->set('b.category', ':toCategory')
            ->where('b.category = :fromCategory')
            ->setParameter('fromCategory', $fromCategory)
            ->setParameter('toCategory', $toCategory)
            ->getQuery()
            ->execute();
    }

    /**
     * 🔢 Compte le nombre de livres dans une catégorie donnée.
     */
    public function countBooksByCategory(string $category): int
    {
        return (int) $this->createQueryBuilder('b')
            ->select('COUNT(b.id)')
            ->where('b.category = :category')
            ->setParameter('category', $category)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * 📅 Trouve les livres publiés entre deux dates données.
     */
    public function findBooksPublishedBetween(\DateTimeInterface $startDate, \DateTimeInterface $endDate): array
    {
        // Assure que les années respectent la plage 2010-2030
        $startYear = $this->sanitizeYear((int)$startDate->format('Y'));
        $endYear = $this->sanitizeYear((int)$endDate->format('Y'));

        $startDate = new \DateTime("$startYear-01-01");
        $endDate = new \DateTime("$endYear-12-31");

        return $this->createQueryBuilder('b')
            ->where('b.publicationDate BETWEEN :start AND :end')
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate)
            ->orderBy('b.publicationDate', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
