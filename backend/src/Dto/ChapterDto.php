<?php


namespace App\Dto;


class ChapterDto
{
    /** @var CardDto[] $cards  */
    public function __construct(
        private string $name,
        private int $ordering,
        private array $cards,
    )
    {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getOrdering(): int
    {
        return $this->ordering;
    }

    public function getCards(): array
    {
        return $this->cards;
    }
}