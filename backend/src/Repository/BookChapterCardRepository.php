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

    public function findALlByBook(BookId $id, ?int $cardId, int $limit): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $bookmarkUsed = false;

        if (!$cardId) {
            $bookmarkSql = "
            SELECT bcc.id as id FROM bookmark
            LEFT JOIN book_chapter_card bcc on bookmark.card_id = bcc.id
            WHERE book_id = :id
            ";
            $bookmark = $conn->fetchAssociative($bookmarkSql, [
                'id' => $id->getValue()
            ]);
            if ($bookmark && isset($bookmark['id'])) {
                $bookmarkUsed = true;
                $cardId = $bookmark['id'];
            }
        }

        $orderingFrom = 0;
        if ($cardId) {
            $orderingSql = "
            SELECT ordering FROM (
                SELECT bcc.id as id,
                row_number() OVER(ORDER BY bc.ordering, bcc.ordering) as ordering
                FROM book_chapter_card bcc
                RIGHT JOIN book_chapter bc on bcc.chapter_id = bc.id
                WHERE bc.book_id = :book_id
                ORDER BY bc.ordering, bcc.ordering
            ) cards WHERE id = :id
            ";
            $ordering = $conn->fetchOne($orderingSql, [
                'id' => $cardId,
                'book_id' => $id->getValue(),
            ]);
            if ($ordering) {
                $orderingFrom = (int)$ordering;
            }
        }

        if ($bookmarkUsed) {
            $orderingFrom -= 3;
            $orderingFrom = max($orderingFrom, 0);
        }

            $sql = "
            SELECT * FROM (
                SELECT bcc.id  as id,
                bcc.content    as content,
                row_number() OVER(ORDER BY bc.ordering, bcc.ordering) as ordering
                FROM book_chapter_card bcc
                RIGHT JOIN book_chapter bc on bcc.chapter_id = bc.id
                WHERE bc.book_id = :book_id
                ORDER BY bc.ordering, bcc.ordering
            ) cards WHERE ordering > :ordering_from  LIMIT :limit
            ";
            $result = $conn->fetchAllAssociative($sql, [
                'book_id' => $id->getValue(),
                'ordering_from' => $orderingFrom,
                'limit' => $limit,
            ]);

        return [
            'result' => $result,
        ];
    }
}
