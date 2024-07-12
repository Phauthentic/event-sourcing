<?php

declare(strict_types=1);

namespace Phauthentic\EventSourcing\Repository\SnapshotStrategy;

use Phauthentic\EventSourcing\Repository\AggregateDataInterface;

/**
 * Takes a snapshot every N-th version of the aggregate.
 */
class EveryVersionStrategy implements SnapshotStrategyInterface
{
    public function __construct()
    {
    }

    public function isApplicable(AggregateDataInterface $aggregateData): bool
    {
        return true;
    }
}
