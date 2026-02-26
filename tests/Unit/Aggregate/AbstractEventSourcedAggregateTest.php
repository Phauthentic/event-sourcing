<?php

declare(strict_types=1);

namespace Phauthentic\EventSourcing\Test\Unit\Aggregate;

use DateTimeImmutable;
use Generator;
use Phauthentic\EventSourcing\Aggregate\AbstractEventSourcedAggregate;
use Phauthentic\EventSourcing\Aggregate\Attribute\DomainEvents;
use Phauthentic\EventSourcing\Aggregate\Exception\AggregateException;
use Phauthentic\EventSourcing\Aggregate\Exception\AggregateEventVersionMismatchException;
use Phauthentic\EventSourcing\Aggregate\Exception\EventMismatchException;
use Phauthentic\EventSourcing\Aggregate\Exception\MissingEventHandlerException;
use Phauthentic\EventStore\Event;
use PHPUnit\Framework\TestCase;

/**
 *
 */
class AbstractEventSourcedAggregateTest extends TestCase
{
    private ConcreteAggregate $aggregate;

    protected function setUp(): void
    {
        $this->aggregate = new ConcreteAggregate();
    }

    public function testRecordThat(): void
    {
        $this->aggregate->doSomething('test data');

        $events = $this->aggregate->consumeAggregateEvents();
        $this->assertCount(1, $events);
        $this->assertInstanceOf(TestEvent::class, $events[0]);
        $this->assertEquals('test data', $events[0]->getText());
    }

    public function testApplyEvent(): void
    {
        $event = new Event(
            aggregateId: 'test-id',
            aggregateVersion: 1,
            event: 'TestEvent',
            payload: new TestEvent('applied data'),
            createdAt: new DateTimeImmutable()
        );

        $this->aggregate->applyEventsFromHistory([$event]);

        $this->assertEquals('applied data', $this->aggregate->testProperty);
        $this->assertEquals(1, $this->aggregate->getAggregateVersion());
    }

    public function testApplyEventsFromHistory(): void
    {
        $eventsGenerator = function (): Generator {
            yield new Event(
                aggregateId: 'test-id',
                aggregateVersion: 1,
                event: 'TestEvent',
                payload: new TestEvent('data 1'),
                createdAt: new DateTimeImmutable()
            );
            yield new Event(
                aggregateId: 'test-id',
                aggregateVersion: 2,
                event: 'TestEvent',
                payload: new TestEvent('data 2'),
                createdAt: new DateTimeImmutable()
            );
            yield new Event(
                aggregateId: 'test-id',
                aggregateVersion: 3,
                event: 'TestEvent',
                payload: new TestEvent('data 3'),
                createdAt: new DateTimeImmutable()
            );
        };

        $this->aggregate->applyEventsFromHistory($eventsGenerator());

        $this->assertEquals('data 3', $this->aggregate->testProperty);
        $this->assertEquals(3, $this->aggregate->getAggregateVersion());
    }

    public function testApplyEventsFromHistoryWithGenerator(): void
    {
        $eventsGenerator = function (): Generator {
            yield new Event(
                aggregateId: 'test-id',
                aggregateVersion: 1,
                event: 'TestEvent',
                payload: new TestEvent('data 1'),
                createdAt: new DateTimeImmutable()
            );
            yield new Event(
                aggregateId: 'test-id',
                aggregateVersion: 2,
                event: 'TestEvent',
                payload: new TestEvent('data 2'),
                createdAt: new DateTimeImmutable()
            );
            yield new Event(
                aggregateId: 'test-id',
                aggregateVersion: 3,
                event: 'TestEvent',
                payload: new TestEvent('data 3'),
                createdAt: new DateTimeImmutable()
            );
        };

        $this->aggregate->applyEventsFromHistory($eventsGenerator());

        $this->assertEquals('data 3', $this->aggregate->testProperty);
        $this->assertEquals(3, $this->aggregate->getAggregateVersion());
    }

    public function testEventMismatchException(): void
    {
        $this->expectException(EventMismatchException::class);

        $event = new Event(
            aggregateId: 'test-id',
            aggregateVersion: 1,
            event: 'TestEvent',
            payload: new IdentityProvidingTestEvent('data 1'),
            createdAt: new DateTimeImmutable()
        );

        $aggregate = new ConcreteAggregate();
        $aggregate->applyEventsFromHistory([$event]);
    }

    public function testMissingEventHandlerException(): void
    {
        $this->expectException(MissingEventHandlerException::class);
        // phpcs:ignore
        $this->expectExceptionMessage('Handler method `whenMissingEventHandlerEvent` for event `Phauthentic\EventSourcing\Test\Unit\Aggregate\MissingEventHandlerEvent` does not exist in aggregate `Phauthentic\EventSourcing\Test\Unit\Aggregate\ConcreteAggregate`');

        $event = new Event(
            aggregateId: 'test-id',
            aggregateVersion: 1,
            event: 'TestEvent',
            payload: new MissingEventHandlerEvent(),
            createdAt: new DateTimeImmutable()
        );

        $this->aggregate->applyEventsFromHistory([$event]);
    }

    public function testAggregateEventVersionMismatchException(): void
    {
        $this->expectException(AggregateEventVersionMismatchException::class);

        $event1 = new IdentityProvidingTestEvent();
        $event1->aggregateId = 'test-id';

        $event2 = new IdentityProvidingTestEvent();
        $event2->aggregateId = 'test-id';

        $events = [
            new Event(
                aggregateId: 'test-id',
                aggregateVersion: 1,
                event: 'TestEvent',
                payload: $event1,
                createdAt: new DateTimeImmutable()
            ),
            new Event(
                aggregateId: 'test-id',
                aggregateVersion: 6,
                event: 'TestEvent',
                payload: $event2,
                createdAt: new DateTimeImmutable()
            ),
        ];

        $this->aggregate->applyEventsFromHistory($events);
    }

    public function testApplyEventWithWrongVersion(): void
    {
        // First apply a correct event to set version to 1
        $event1 = new Event(
            aggregateId: 'test-id',
            aggregateVersion: 1,
            event: 'TestEvent',
            payload: new TestEvent('test1'),
            createdAt: new DateTimeImmutable()
        );
        $this->aggregate->applyEventsFromHistory([$event1]);

        // Now try to apply an event with version 3 (skipping version 2)
        $event2 = new Event(
            aggregateId: 'test-id',
            aggregateVersion: 3,
            event: 'TestEvent',
            payload: new TestEvent('test2'),
            createdAt: new DateTimeImmutable()
        );

        $this->expectException(AggregateEventVersionMismatchException::class);
        $this->aggregate->applyEventsFromHistory([$event2]);
    }

    public function testGetAggregateId(): void
    {
        $this->assertEquals('test-id', $this->aggregate->getAggregateId());
    }

    public function testConsumeAggregateEvents(): void
    {
        $this->aggregate->doSomething('test data');

        // First call should return the events
        $events = $this->aggregate->consumeAggregateEvents();
        $this->assertCount(1, $events);
        $this->assertInstanceOf(TestEvent::class, $events[0]);

        // Second call should return empty array since events were consumed
        $events = $this->aggregate->consumeAggregateEvents();
        $this->assertCount(0, $events);
    }

    public function testImplementsEventSourcedAggregateInterface(): void
    {
        $this->assertInstanceOf(\Phauthentic\EventSourcing\Aggregate\EventSourcedAggregateInterface::class, $this->aggregate);
    }

    public function testNoDoubleVersionIncrement(): void
    {
        $this->aggregate->doSomething('test data');

        // Version should be 1 after recording one event
        $this->assertEquals(1, $this->aggregate->getAggregateVersion());

        // After consuming events, version should remain 1 (no additional increment)
        $this->aggregate->consumeAggregateEvents();
        $this->assertEquals(1, $this->aggregate->getAggregateVersion());
    }

    public function testRecordThatRequiresAggregateId(): void
    {
        $aggregate = new class extends AbstractEventSourcedAggregate {
            #[DomainEvents]
            protected array $aggregateEvents = [];

            public function getAggregateVersion(): int
            {
                return $this->aggregateVersion;
            }

            public function doSomething(): void
            {
                $this->recordThat(new TestEvent('test'));
            }
        };

        $this->expectException(AggregateException::class);
        $this->expectExceptionMessage('Aggregate ID must be set before recording events');
        $aggregate->doSomething();
    }
}
