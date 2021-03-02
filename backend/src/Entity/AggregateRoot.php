<?php


namespace App\Entity;


interface AggregateRoot
{
    public function releaseEvents(): array;
}