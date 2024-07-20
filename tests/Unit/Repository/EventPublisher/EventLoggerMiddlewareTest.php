<?php

declare(strict_types=1);

namespace Phauthentic\EventSourcing\Test\Unit\Repository\EventPublisher;

use Phauthentic\EventSourcing\Repository\EventPublisher\EventLoggerMiddleware;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 *
 */
class EventLoggerMiddlewareTest extends TestCase
{
    public function testHandleLogsEventInfo(): void
    {
        $mockLogger = $this->createMock(LoggerInterface::class);

        $middleware = new EventLoggerMiddleware($mockLogger);

        $dummyEvent = new class () {
        };

        $mockLogger->expects($this->once())
            ->method('info')
            ->with($this->equalTo(sprintf('Event %s emitted.', get_class($dummyEvent))));

        $middleware->handle($dummyEvent);
    }

    public function testIsInterrupting(): void
    {
        $mockLogger = $this->createMock(LoggerInterface::class);

        $middleware = new EventLoggerMiddleware($mockLogger);

        $this->assertFalse($middleware->isInterrupting());
    }
}
