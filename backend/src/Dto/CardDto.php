<?php


namespace App\Dto;


class CardDto
{
    public function __construct(
        private string $content,
        private int $ordering,
    )
    {
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getOrdering(): int
    {
        return $this->ordering;
    }
}