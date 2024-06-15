<?php

declare(strict_types=1);

namespace Phauthentic\EventSourcing\Repository\AggregateFactory;

use Iterator;
use Phauthentic\SnapshotStore\SnapshotInterface;

/**
 *
 */
interface AggregateFactoryInterface
{
    /**
     *
     */
    public function reconstituteFromEvents(string|object $aggregate, Iterator $events): object;
}
