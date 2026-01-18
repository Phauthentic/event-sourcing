<?php

declare(strict_types=1);

namespace Phauthentic\EventSourcing\Test\Unit\Repository\AggregateFactory;

use ArrayIterator;

/**
 * Test aggregate for ReflectionFactory testing
 */
class TestAggregate
{
    public function applyEventsFromHistory(ArrayIterator $events): void
    {
        // Mock implementation - do nothing
    }
}
