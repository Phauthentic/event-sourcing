<?php

declare(strict_types=1);

namespace Phauthentic\EventSourcing\Repository\SnapshotStrategy;

use Phauthentic\EventSourcing\Repository\AggregateDataInterface;

/**
 * Takes a snapshot if certain events occur.
 */
class OnEventStrategy implements SnapshotStrategyInterface
{
    /**
     * @param array<int, string> $eventNames
     */
    public function __construct(
        protected array $eventNames = []
    ) {
    }

    public function isApplicable(AggregateDataInterface $aggregateData): bool
    {
        $events = $aggregateData->getDomainEvents();
        $events = array_reverse($events, true);

        foreach ($events as $event) {
            if ($this->eventNameMatches((string)get_class($event))) {
                return true;
            }
        }

        return false;
    }

    protected function eventNameMatches(string $eventName): bool
    {
        return in_array($eventName, $this->eventNames, true);
    }
}
