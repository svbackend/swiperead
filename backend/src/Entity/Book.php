<?php

namespace App\Entity;

use App\Dto\ChapterDto;
use App\Entity\User\User;
use App\Repository\BookRepository;
use App\ValueObject\Book\BookId;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

/**
 * @ORM\Entity(repositoryClass=BookRepository::class)
 */
class Book
{
    /**
     * @ORM\Id
     * @ORM\Column(type="book_id")
     */
    private BookId $id;

    /**
     * @ORM\Column(type="string", length=1000)
     */
    private string $title;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private User $owner;

    /**
     * @ORM\OneToMany(targetEntity=BookAuthor::class, mappedBy="book", cascade={"persist", "remove"})
     */
    private Collection $authors;

    /**
     * @ORM\OneToMany(targetEntity=BookChapter::class, mappedBy="book", orphanRemoval=true, cascade={"persist", "remove"})
     */
    private Collection $chapters;

    /**
     * @param Collection<int, Author> $authors
     * @param Collection<int, ChapterDto> $chapters
     */
    public function __construct(
        BookId $id,
        string $title,
        User $owner,
        Collection $authors,
        Collection $chapters,
    )
    {
        Assert::notEmpty($title);
        Assert::false($authors->isEmpty(), 'At least 1 author required');
        Assert::false($chapters->isEmpty(), 'At least 1 chapter required');
        $this->id = $id;
        $this->title = $title;
        $this->owner = $owner;
        $this->authors = $authors->map(fn(Author $author) => new BookAuthor($this, $author));

        $this->chapters = $chapters->map(fn(ChapterDto $dto) => new BookChapter(
            $dto->getName(),
            $this,
            $dto->getOrdering(),
            new ArrayCollection($dto->getCards())
        ));
    }
}
