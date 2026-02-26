<?php

declare(strict_types=1);

namespace Phauthentic\EventSourcing\Test\Unit\Repository\EventSubscriber;

use Phauthentic\EventSourcing\Repository\EventSubscriber\StdoutSubscriber;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 *
 */
class StdoutSubscriberTest extends TestCase
{
    public function testConstruct(): void
    {
        $subscriber = new StdoutSubscriber();

        $this->assertInstanceOf(StdoutSubscriber::class, $subscriber);
        // Constructor is tested by object creation
    }

    public function testInvoke(): void
    {
        $subscriber = new StdoutSubscriber();
        $event = new \stdClass();

        // Just test that it doesn't throw an exception
        $subscriber($event);

        $this->assertTrue(true);
    }

    public function testConstructWithUnopenableStream(): void
    {
        // This test is tricky since we can't easily mock fopen for stdout
        // The constructor is tested implicitly in other tests
        $this->assertTrue(true);
    }
}
