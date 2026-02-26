<?php

declare(strict_types=1);

namespace Phauthentic\EventSourcing\Test\Unit\Repository\AggregateFactory;

use ArrayIterator;
use Phauthentic\EventSourcing\Repository\AggregateFactory\ReflectionFactory;
use Phauthentic\EventSourcing\Repository\EventSourcedRepositoryException;
use Phauthentic\EventSourcing\Test\Unit\Aggregate\ConcreteAggregate;
use Phauthentic\EventSourcing\Test\Unit\Aggregate\TestEvent;
use Phauthentic\SnapshotStore\SnapshotInterface;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 *
 */
class ReflectionFactoryTest extends TestCase
{
    public function testConstructWithDefaults(): void
    {
        $factory = new ReflectionFactory();

        $this->assertInstanceOf(ReflectionFactory::class, $factory);
    }

    public function testConstructWithCustomParameters(): void
    {
        $factory = new ReflectionFactory('customMethod', ['key' => 'value']);

        $this->assertInstanceOf(ReflectionFactory::class, $factory);
    }

    public function testReconstituteFromEventsWithObject(): void
    {
        $aggregate = new TestAggregate();
        $events = new ArrayIterator([]);

        $factory = new ReflectionFactory();
        $result = $factory->reconstituteFromEvents($aggregate, $events);

        $this->assertSame($aggregate, $result);
    }

    public function testReconstituteFromEventsWithString(): void
    {
        $events = new ArrayIterator([]);

        $factory = new ReflectionFactory();
        $result = $factory->reconstituteFromEvents(TestAggregate::class, $events);

        $this->assertInstanceOf(TestAggregate::class, $result);
    }

    public function testReconstituteFromEventsWithClassMap(): void
    {
        $events = new ArrayIterator([]);

        $factory = new ReflectionFactory('applyEventsFromHistory', ['mapped' => TestAggregate::class]);
        $result = $factory->reconstituteFromEvents('mapped', $events);

        $this->assertInstanceOf(TestAggregate::class, $result);
    }

    public function testReconstituteFromEventsWithSnapshot(): void
    {
        $events = new ArrayIterator([]);

        $snapshot = $this->createMock(SnapshotInterface::class);
        $aggregate = new TestAggregate();
        $snapshot->method('getAggregateRoot')->willReturn($aggregate);

        $factory = new ReflectionFactory();
        $result = $factory->reconstituteFromEvents($snapshot, $events);

        $this->assertSame($aggregate, $result);
    }

    public function testReconstituteFromEventsWithMissingMethod(): void
    {
        $aggregate = new stdClass();
        $events = new ArrayIterator([]);

        $factory = new ReflectionFactory();

        $this->expectException(EventSourcedRepositoryException::class);
        $this->expectExceptionMessage('does not have a method `applyEventsFromHistory`');

        $factory->reconstituteFromEvents($aggregate, $events);
    }

    public function testReconstituteFromEventsWithInvalidClass(): void
    {
        $events = new ArrayIterator([]);

        $factory = new ReflectionFactory();

        $this->expectException(\ReflectionException::class);

        $factory->reconstituteFromEvents('InvalidClassName', $events);
    }

    public function testReconstituteFromEventsWithCustomMethodName(): void
    {
        $aggregate = new TestAggregate();
        $events = new ArrayIterator([]);

        $factory = new ReflectionFactory('applyEventsFromHistory');
        $result = $factory->reconstituteFromEvents($aggregate, $events);

        $this->assertSame($aggregate, $result);
    }
}
