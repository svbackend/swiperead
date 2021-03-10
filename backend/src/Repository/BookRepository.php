<?php

namespace App\Repository;

use App\Entity\Book;
use App\Utils\Json;
use App\ValueObject\Book\BookId;
use App\ValueObject\User\UserId;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    public function add(Book $book): void
    {
        $this->getEntityManager()->persist($book);
    }

    public function findAllByOwner(UserId $id): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "
        SELECT 
               book.id as id, 
               book.title as title, 
               a.authors as authors 
        FROM book 
        LEFT JOIN (
            SELECT book_id, json_agg(json_build_object('id', author.id, 'name', author.name)) authors FROM book_author ba
            LEFT JOIN author ON (author.id = ba.author_id)
            GROUP BY ba.book_id
        ) a ON a.book_id = book.id
        WHERE book.owner_id = :owner_id
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

    public function upsertBookmark(BookId $bookId, int $cardId): void
    {
        $conn = $this->getEntityManager()->getConnection();

        $conn->beginTransaction();

        try {
            $conn->executeStatement('DELETE FROM bookmark WHERE book_id = :book_id;', [
                ':book_id' => $bookId->getValue(),
            ]);
            $conn->executeStatement('
            INSERT INTO bookmark (book_id, card_id) VALUES (:book_id, :card_id) ON CONFLICT DO NOTHING;', [
                ':book_id' => $bookId->getValue(),
                ':card_id' => $cardId,
            ]);
        } catch (\Throwable $e) {
            $conn->rollBack();
            throw $e;
        }

        $conn->commit();
    }
}
