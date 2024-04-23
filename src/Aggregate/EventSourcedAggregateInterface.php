<?php

declare(strict_types=1);

namespace Phauthentic\EventSourcing\Aggregate;

/**
 *
 */
interface EventSourcedAggregateInterface
{
    /**
     * Returns the ID of the aggregate as string
     */
    public function getAggregateId(): string;

    /**
     * Get the version of the aggregate
     */
    public function getAggregateVersion(): int;

    /**
     * - Returns a list of events
     * - Resets the events to an empty list
     *
     * @return array<int, object>
     */
    public function consumeAggregateEvents(): array;
}
