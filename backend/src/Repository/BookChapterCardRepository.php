<?php

namespace App\Repository;

use App\Entity\BookChapterCard;
use App\Utils\Json;
use App\ValueObject\Book\BookId;
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

    public function findALlByBook(BookId $id): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "
        SELECT id, content, ordering 
        FROM book_chapter_card bcc 
        WHERE bcc.chapter_id = x
        ";
        $result = $conn->fetchAllAssociative($sql, [
            'owner_id' => $id->getValue()
        ]);

        return [
            'result' => array_map(static fn(array $row) => [
                    'authors' => Json::decode($row['authors'])
                ] + $row, $result),
        ];
    }
}
