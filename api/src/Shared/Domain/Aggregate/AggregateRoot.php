<?php

declare(strict_types=1);

namespace App\Shared\Domain\Aggregate;

use App\Shared\Domain\Event\DomainEventInterface;

abstract class AggregateRoot
{
    protected array $domainEvents;

    public function recordDomainEvent(DomainEventInterface $domainEvent): self
    {
        $this->domainEvents[] = $domainEvent;

        return $this;
    }

    public function pullDomainEvents(): array
    {
        $domainEvents = $this->domainEvents;
        $this->domainEvents = [];

        return $domainEvents;
    }
}
