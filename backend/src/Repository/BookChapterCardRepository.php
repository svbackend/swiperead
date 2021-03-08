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

    public function findALlByBook(BookId $id, int $offset, int $limit): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $bookmarkSql = "
            SELECT 
                   bcc.id as id,
                   bcc.ordering as ordering,
                   bcc.chapter_id as chapter_id
            FROM bookmark
            LEFT JOIN book_chapter_card bcc on bookmark.card_id = bcc.id
            WHERE book_id = :id
            ";
        $bookmark = $conn->fetchOne($bookmarkSql, [
            'id' => $id->getValue()
        ]);

        if ($bookmark === false) {
            $sql = "
            SELECT id, content, ordering 
            FROM book_chapter_card bcc 
            WHERE bcc.chapter_id = (SELECT bc.id FROM book_chapter bc WHERE bc.book_id = :id ORDER BY bc.ordering LIMIT 1)
            ORDER BY ordering OFFSET :offset LIMIT :limit
            ";
            $result = $conn->fetchAllAssociative($sql, [
                'id' => $id->getValue(),
                'offset' => $offset,
                'limit' => $limit,
            ]);
        } else {
            $sql = "
            SELECT id, content, ordering 
            FROM book_chapter_card bcc 
            WHERE bcc.chapter_id = :chapter_id
            ORDER BY ordering OFFSET :offset LIMIT :limit
            ";
            $result = $conn->fetchAllAssociative($sql, [
                'owner_id' => $id->getValue(),
                'chapter_id' => $bookmark['chapter_id'],
                'offset' => $offset,
                'limit' => $limit,
            ]);
        }

        return [
            'result' => $result,
        ];
    }
}
