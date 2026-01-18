<?php

declare(strict_types=1);

namespace Phauthentic\EventSourcing\Test\Unit\Repository\AggregateExtractor;

use Phauthentic\EventSourcing\Aggregate\EventSourcedAggregateInterface;
use Phauthentic\EventSourcing\Aggregate\TypeProvidingAggregateInterface;

/**
 *
 */
class AggregateWithInterfaces implements EventSourcedAggregateInterface, TypeProvidingAggregateInterface
{
    /**
     * @param string $aggregateId
     * @param int $aggregateVersion
     * @param string $aggregateType
     * @param array<int, object> $events
     */
    public function __construct(
        private string $aggregateId = '123',
        private int $aggregateVersion = 1,
        private string $aggregateType = 'AggregateWithInterfaces',
        private array $events = [],
    ) {
    }

    public function getAggregateId(): string
    {
        return $this->aggregateId;
    }

    public function getAggregateVersion(): int
    {
        return $this->aggregateVersion;
    }

    public function consumeAggregateEvents(): array
    {
        return $this->events;
    }

    public function getAggregateType(): string
    {
        return $this->aggregateType;
    }
}
