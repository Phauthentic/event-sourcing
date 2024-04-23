<?php

declare(strict_types=1);

namespace Phauthentic\EventSourcing\DomainEvent;

/**
 *
 */
abstract class AbstractDomainEvent implements
    AggregateIdentityProvidingEventInterface,
    AggregateVersionProvidingEvent,
    TypeProvidingDomainEventInterface
{
    protected string $aggregateId;

    protected int $aggregateVersion;

    protected string $domainEventType;

    public function getAggregateId(): string
    {
        return $this->aggregateId;
    }

    public function getAggregateVersion(): int
    {
        return $this->aggregateVersion;
    }

    public function getEventType(): string
    {
        return $this->domainEventType;
    }
}
