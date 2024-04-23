<?php

declare(strict_types=1);

namespace Phauthentic\EventSourcing\Aggregate;

/**
 * If the aggregate should provide a stream name use this interface.
 */
interface StreamNameProvidingAggregateInterface
{
    public function getEventStreamName(): string;
}
