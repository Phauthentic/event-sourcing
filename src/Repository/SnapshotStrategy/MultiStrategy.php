<?php

declare(strict_types=1);

namespace Phauthentic\EventSourcing\Repository\SnapshotStrategy;

use Phauthentic\EventSourcing\Repository\AggregateDataInterface;

/**
 *
 */
class MultiStrategy implements SnapshotStrategyInterface
{
    /**
     * @var array<int, SnapshotStrategyInterface> $strategies
     */
    protected array $strategies = [];

    /**
     * @param array<int, SnapshotStrategyInterface> $strategies
     */
    public function __construct(
        array $strategies
    ) {
        foreach ($strategies as $strategy) {
            $this->addStrategy($strategy);
        }
    }

    public function isApplicable(AggregateDataInterface $aggregateData): bool
    {
        foreach ($this->strategies as $strategy) {
            if ($strategy->isApplicable($aggregateData)) {
                return true;
            }
        }

        return false;
    }

    protected function addStrategy(SnapshotStrategyInterface $snapshotStrategy): void
    {
        $this->strategies[] = $snapshotStrategy;
    }
}
