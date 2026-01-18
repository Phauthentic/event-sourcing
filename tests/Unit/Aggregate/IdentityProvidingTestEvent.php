<?php

declare(strict_types=1);

namespace Phauthentic\EventSourcing\Test\Unit\Aggregate;

use Phauthentic\EventSourcing\DomainEvent\AggregateIdentityProvidingEventInterface;

/**
 *
 */
class IdentityProvidingTestEvent extends TestEvent implements AggregateIdentityProvidingEventInterface
{
    public string $aggregateId = '';

    public int $aggregateVersion = 1;

    public function getAggregateId(): string
    {
        return $this->aggregateId;
    }

    public function getAggregateVersion(): int
    {
        return $this->aggregateVersion;
    }
}
