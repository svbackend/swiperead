<?php


namespace App\Entity;


use App\Event\DomainEvent;

trait AggregateRootTrait
{
    /** @var $events DomainEvent[] */
    private array $events = [];

    protected function storeEvent(DomainEvent $e): void
    {
        $this->events[] = $e;
    }

    /** @return DomainEvent[] */
    public function releaseEvents(): array
    {
        $events = $this->events;
        $this->events = [];
        return $events;
    }
}