<?php

declare(strict_types=1);

namespace Phauthentic\EventSourcing\Test\Unit\Repository\EventSubscriber;

use Phauthentic\EventSourcing\Repository\EventSubscriber\LogSubscriber;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 *
 */
class LogSubscriberTest extends TestCase
{
    public function testConstruct(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $subscriber = new LogSubscriber($logger);

        $this->assertInstanceOf(LogSubscriber::class, $subscriber);
    }

    public function testInvoke(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())
            ->method('info')
            ->with($this->stringContains('Event emitted: stdClass'));

        $subscriber = new LogSubscriber($logger);
        $event = new \stdClass();

        $subscriber($event);
    }
}
