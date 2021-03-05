<?php

namespace App\Entity;

use App\Repository\BookAuthorRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BookAuthorRepository::class)
 */
class BookAuthor
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /** @ORM\ManyToOne(targetEntity=Book::class, inversedBy="authors") */
    private Book $book;

    /** @ORM\ManyToOne(targetEntity=Author::class, inversedBy="books") */
    private Author $author;

    public function __construct(Book $book, Author $author)
    {
        $this->book = $book;
        $this->author = $author;
    }
}
