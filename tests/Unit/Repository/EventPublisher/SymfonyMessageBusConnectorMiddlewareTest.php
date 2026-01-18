<?php

declare(strict_types=1);

namespace Phauthentic\EventSourcing\Test\Unit\Repository\EventPublisher;

use Phauthentic\EventSourcing\Repository\EventPublisher\SymfonyMessageBusConnectorMiddleware;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBus;

/**
 *
 */
class SymfonyMessageBusConnectorMiddlewareTest extends TestCase
{
    public function testHandleDispatchesEventToMessageBus(): void
    {
        $mockMessageBus = $this->createMock(MessageBus::class);

        $middleware = new SymfonyMessageBusConnectorMiddleware($mockMessageBus);

        $dummyEvent = new class () {
        };

        $mockMessageBus->expects($this->once())
            ->method('dispatch')
            ->with($this->identicalTo($dummyEvent))
            ->willReturn(new Envelope($dummyEvent));

        $middleware->handle($dummyEvent);
    }

    public function testIsInterrupting(): void
    {
        $mockMessageBus = $this->createMock(MessageBus::class);

        $middleware = new SymfonyMessageBusConnectorMiddleware($mockMessageBus);

        $this->assertFalse($middleware->isInterrupting());
    }
}
