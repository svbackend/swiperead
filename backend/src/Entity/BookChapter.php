<?php

namespace App\Entity;

use App\Dto\CardDto;
use App\Repository\BookChapterRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

/**
 * @ORM\Entity(repositoryClass=BookChapterRepository::class)
 */
class BookChapter
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=1000)
     */
    private string $name;

    /**
     * @ORM\ManyToOne(targetEntity=Book::class, inversedBy="chapters")
     * @ORM\JoinColumn(nullable=false)
     */
    private Book $book;

    /**
     * @ORM\OneToMany(targetEntity=BookChapterCard::class, mappedBy="chapter", orphanRemoval=true, cascade={"persist", "remove"})
     */
    private Collection $cards;

    /**
     * @ORM\Column(type="integer")
     */
    private int $ordering;

    /** @param Collection<int, CardDto> $cards */
    public function __construct(string $name, Book $book, int $ordering, Collection $cards)
    {
        Assert::false($cards->isEmpty(), 'At least 1 card required');
        $this->name = $name;
        $this->book = $book;
        $this->cards = $cards->map(fn(CardDto $card) => new BookChapterCard(
            $card->getContent(), $card->getOrdering(), $this)
        );
        $this->ordering = $ordering;
    }
}
