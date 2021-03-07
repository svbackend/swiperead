<?php

namespace App\Entity;

use App\Repository\BookmarkRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BookmarkRepository::class)
 * @ORM\Table(name="bookmark", uniqueConstraints={
 *     @ORM\UniqueConstraint(columns={"book_id", "card_id"})
 * })
 */
class Bookmark
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\OneToOne(targetEntity=Book::class, cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private Book $book;

    /**
     * @ORM\OneToOne(targetEntity=BookChapterCard::class, cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private BookChapterCard $card;

    public function __construct(Book $book, BookChapterCard $card)
    {
        $this->book = $book;
        $this->card = $card;
    }
}
