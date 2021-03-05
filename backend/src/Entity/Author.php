<?php

namespace App\Entity;

use App\ValueObject\Author\AuthorId;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

/**
 * @ORM\Entity()
 */
class Author
{
    /**
     * @ORM\Id
     * @ORM\Column(type="author_id")
     */
    private AuthorId $id;

    /**
     * @ORM\Column(type="string", length=1000)
     */
    private string $name;

    /**
     * @ORM\OneToMany(targetEntity=BookAuthor::class, mappedBy="author")
     */
    private Collection $books;

    public function __construct(AuthorId $id, string $name)
    {
        Assert::notEmpty($name);
        $this->id = $id;
        $this->name = $name;
    }
}
