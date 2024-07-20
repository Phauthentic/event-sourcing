<?php

declare(strict_types=1);

namespace Phauthentic\EventSourcing\Test\Unit\Repository\EventPublisher;

use Phauthentic\EventSourcing\Repository\EventPublisher\EventPublisher;
use Phauthentic\EventSourcing\Repository\EventPublisher\EventPublisherMiddlewareInterface;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use RuntimeException;

/**
 *
 */
class EventPublisherTest extends TestCase
{
    public function testConstructorWithEmptyMiddlewareArrayThrowsException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('No middleware registered!');

        new EventPublisher([]);
    }

    public function testAddMiddleware(): void
    {
        $publisher = new EventPublisher([
            $this->createMock(EventPublisherMiddlewareInterface::class)
        ]);

        $newMiddleware = $this->createMock(EventPublisherMiddlewareInterface::class);
        $publisher->addMiddleware($newMiddleware);

        $reflectionClass = new ReflectionClass($publisher);
        $this->assertCount(2, $reflectionClass->getProperty('middlewares')->getValue($publisher));
    }

    public function testEmitEventCallsAllMiddlewares(): void
    {
        $middleware1 = $this->createMock(EventPublisherMiddlewareInterface::class);
        $middleware2 = $this->createMock(EventPublisherMiddlewareInterface::class);

        $middleware1->method('isInterrupting')->willReturn(false);
        $middleware2->method('isInterrupting')->willReturn(false);

        $publisher = new EventPublisher([$middleware1, $middleware2]);

        $event = new \stdClass();

        $middleware1->expects($this->once())->method('handle')->with($event);
        $middleware2->expects($this->once())->method('handle')->with($event);

        $publisher->emitEvent($event);
    }

    public function testEmitEventStopsOnInterruptingMiddleware(): void
    {
        $middleware1 = $this->createMock(EventPublisherMiddlewareInterface::class);
        $middleware2 = $this->createMock(EventPublisherMiddlewareInterface::class);

        $middleware1->method('isInterrupting')->willReturn(true);

        $publisher = new EventPublisher([$middleware1, $middleware2]);

        $event = new \stdClass();

        $middleware1->expects($this->once())->method('handle')->with($event);
        $middleware2->expects($this->never())->method('handle');

        $publisher->emitEvent($event);
    }

    public function testEmitEventsCallsEmitEventForEachEvent(): void
    {
        $middleware = $this->createMock(EventPublisherMiddlewareInterface::class);
        $middleware->method('isInterrupting')->willReturn(false);
        $publisher = new EventPublisher([$middleware]);

        $events = [new \stdClass(), new \stdClass(), new \stdClass()];

        $middleware->expects($this->exactly(3))->method('handle');

        $publisher->emitEvents($events);
    }

    public function testEmitEventsWorksWithIterator(): void
    {
        $middleware = $this->createMock(EventPublisherMiddlewareInterface::class);
        $middleware->method('isInterrupting')->willReturn(false);
        $publisher = new EventPublisher([$middleware]);

        $events = new \ArrayIterator([new \stdClass(), new \stdClass()]);

        $middleware->expects($this->exactly(2))->method('handle');

        $publisher->emitEvents($events);
    }

    public function testEmitEventsWorksWithGenerator(): void
    {
        $middleware = $this->createMock(EventPublisherMiddlewareInterface::class);
        $middleware->method('isInterrupting')->willReturn(false);
        $publisher = new EventPublisher([$middleware]);

        $events = (function () {
            yield new \stdClass();
            yield new \stdClass();
        })();

        $middleware->expects($this->exactly(2))->method('handle');

        $publisher->emitEvents($events);
    }
}
