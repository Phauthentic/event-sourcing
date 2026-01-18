<?php

declare(strict_types=1);

namespace Phauthentic\EventSourcing\Test\Unit\Aggregate\DomainEvent;

use Phauthentic\EventSourcing\DomainEvent\AbstractDomainEvent;

/**
 * A concrete implementation of AbstractDomainEvent for testing
 */
class ConcreteDomainEvent extends AbstractDomainEvent
{
    public function __construct(string $aggregateId, int $aggregateVersion, string $domainEventType)
    {
        $this->aggregateId = $aggregateId;
        $this->aggregateVersion = $aggregateVersion;
        $this->domainEventType = $domainEventType;
    }
}
