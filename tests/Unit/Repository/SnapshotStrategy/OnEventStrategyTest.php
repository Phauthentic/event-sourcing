<?php

declare(strict_types=1);

namespace Phauthentic\EventSourcing\Test\Unit\Repository\SnapshotStrategy;

use Phauthentic\EventSourcing\Repository\AggregateDataInterface;
use Phauthentic\EventSourcing\Repository\SnapshotStrategy\OnEventStrategy;
use Phauthentic\EventSourcing\Test\Unit\Aggregate\TestEvent;
use Phauthentic\EventSourcing\Test\Unit\Aggregate\TestEvent2;
use PHPUnit\Framework\TestCase;

/**
 *
 */
class OnEventStrategyTest extends TestCase
{
    public function testIsApplicableWithMatchingEvent(): void
    {
        $eventNames = [TestEvent::class, TestEvent2::class];
        $strategy = new OnEventStrategy($eventNames);

        $mockEvent1 = new TestEvent();
        $mockEvent2 = new TestEvent2();

        $aggregateData = $this->createMock(AggregateDataInterface::class);
        $aggregateData->method('getDomainEvents')->willReturn([
            $mockEvent1,
            $mockEvent2
        ]);

        $result = $strategy->isApplicable($aggregateData);

        $this->assertTrue($result);
    }

    public function testIsApplicableWithNonMatchingEvents(): void
    {
        $eventNames = ['App\Events\UserDeleted', 'App\Events\OrderCancelled'];
        $strategy = new OnEventStrategy($eventNames);

        $mockEvent1 = new class () {
        };
        $mockEvent2 = new class () {
        };

        $aggregateData = $this->createMock(AggregateDataInterface::class);
        $aggregateData->method('getDomainEvents')->willReturn([
            $mockEvent1,
            $mockEvent2
        ]);

        $result = $strategy->isApplicable($aggregateData);

        $this->assertFalse($result);
    }

    public function testIsApplicableWithEmptyEventList(): void
    {
        $strategy = new OnEventStrategy(['App\Events\UserCreated']);

        $aggregateData = $this->createMock(AggregateDataInterface::class);
        $aggregateData->method('getDomainEvents')->willReturn([]);

        $result = $strategy->isApplicable($aggregateData);

        $this->assertFalse($result);
    }

    public function testIsApplicableWithEmptyEventNames(): void
    {
        $strategy = new OnEventStrategy([]);

        $mockEvent = new class () {
        };

        $aggregateData = $this->createMock(AggregateDataInterface::class);
        $aggregateData->method('getDomainEvents')->willReturn([$mockEvent]);

        $result = $strategy->isApplicable($aggregateData);

        $this->assertFalse($result);
    }
}
