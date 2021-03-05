<?php

namespace App\Repository;

use App\Entity\BookChapterCard;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BookChapterCard|null find($id, $lockMode = null, $lockVersion = null)
 * @method BookChapterCard|null findOneBy(array $criteria, array $orderBy = null)
 * @method BookChapterCard[]    findAll()
 * @method BookChapterCard[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookChapterCardRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BookChapterCard::class);
    }

    // /**
    //  * @return BookChapterCard[] Returns an array of BookChapterCard objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?BookChapterCard
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
