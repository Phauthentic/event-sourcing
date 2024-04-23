<?php

declare(strict_types=1);

namespace Phauthentic\EventSourcing\Repository\SnapshotStrategy;

use Phauthentic\EventSourcing\Repository\AggregateDataInterface;

/**
 *
 */
class EveryNthVersionStrategy implements SnapshotStrategyInterface
{
    public function __construct(
        protected int $modulus = 5
    ) {
    }

    public function isApplicable(AggregateDataInterface $aggregateData): bool
    {
        return $aggregateData->getAggregateVersion() % $this->modulus === 0;
    }
}
