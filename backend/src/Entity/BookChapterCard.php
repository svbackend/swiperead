<?php

namespace App\Entity;

use App\Repository\BookChapterCardRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BookChapterCardRepository::class)
 */
class BookChapterCard
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="text")
     */
    private string $content;

    /**
     * @ORM\Column(type="integer")
     */
    private int $ordering;

    /**
     * @ORM\ManyToOne(targetEntity=BookChapter::class, inversedBy="cards")
     * @ORM\JoinColumn(nullable=false)
     */
    private BookChapter $chapter;

    public function __construct(string $content, string $ordering, BookChapter $chapter)
    {
        $this->content = $content;
        $this->ordering = $ordering;
        $this->chapter = $chapter;
    }
}
