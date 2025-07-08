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

    //    /**
    //     * @return Book[] Returns an array of Book objects
    //     */
    public function findAllWithPage($nbPage, $limitNbBooks, $orderBy = 'b.title', $direction = 'ASC'): array
    {
        $query = $this->createQueryBuilder('b') // équivaut à SELECT b FROM Book b
            ->orderBy($orderBy, $direction)
            ->setFirstResult(($nbPage - 1) * $limitNbBooks)
            // calcul à partir de quel resultat commence "OFFSET"
            // (($nbPage - 1) * $limitNbBooks)
            // ((1 - 1) * 3) -> = 0.    -> on commence à partir de 0, cad page 1
            // ((2 - 1) * 10) -> = 10.    -> on commence à partir de 10, cad page 2
            // ((3 - 1) * 10) -> = 20.    -> on commence à partir de 20, cad page 3
            ->setMaxResults($limitNbBooks); // définit le nb max de livres à récupérer

        return $query->getQuery()->getResult(); // execute la requete et retourne tableau
    }

    //    public function findOneBySomeField($value): ?Book
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    // http://127.0.0.1:8000/books?page=5&limit=3

}
