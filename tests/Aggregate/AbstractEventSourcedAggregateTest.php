<?php

declare(strict_types=1);

namespace Phauthentic\EventSourcing\Test\Aggregate;

use DateTimeImmutable;
use Generator;
use Phauthentic\EventSourcing\Aggregate\AbstractEventSourcedAggregate;
use Phauthentic\EventSourcing\Aggregate\Exception\AggregateException;
use Phauthentic\EventStore\Event;
use PHPUnit\Framework\TestCase;
use Phauthentic\EventSourcing\Aggregate\Exception\AggregateEventVersionMismatchException;
use Phauthentic\EventSourcing\Aggregate\Exception\EventMismatchException;
use Phauthentic\EventSourcing\Aggregate\Exception\MissingEventHandlerException;
use ReflectionClass;
use ReflectionMethod;

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

        $events = $this->aggregate->getAggregateEvents();
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

        $testEvent = new IdentityProvidingTestEvent('data 1');
        $testEvent->aggregateId = 'wrong-id';
        $testEvent->aggregateVersion = 1;

        $event = new Event(
            aggregateId: 'test-id',
            aggregateVersion: 1,
            event: 'TestEvent',
            payload: $testEvent,
            createdAt: new DateTimeImmutable()
        );

        $this->aggregate->applyEventsFromHistory([$event]);
    }

    public function testMissingEventHandlerException(): void
    {
        $this->expectException(MissingEventHandlerException::class);
        // phpcs:ignore
        $this->expectExceptionMessage('Handler method `whenMissingEventHandlerEvent` for event `Phauthentic\EventSourcing\Test\Aggregate\MissingEventHandlerEvent` does not exist in aggregate `Phauthentic\EventSourcing\Test\Aggregate\ConcreteAggregate`');

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
}
