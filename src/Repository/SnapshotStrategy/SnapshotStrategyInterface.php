<?php

declare(strict_types=1);

namespace Phauthentic\EventSourcing\Repository\SnapshotStrategy;

use Phauthentic\EventSourcing\Repository\AggregateDataInterface;

/**
 * The event sourced repository can be used directly or extended.
 */
interface SnapshotStrategyInterface
{
    public function isApplicable(AggregateDataInterface $aggregateData): bool;
}
