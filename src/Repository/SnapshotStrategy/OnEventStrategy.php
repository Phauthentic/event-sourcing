<?php

/**
 * Copyright (c) Florian Krämer (https://florian-kraemer.net)
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE file
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Florian Krämer (https://florian-kraemer.net)
 * @author    Florian Krämer
 * @link      https://github.com/Phauthentic
 * @license   https://opensource.org/licenses/MIT MIT License
 */

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
