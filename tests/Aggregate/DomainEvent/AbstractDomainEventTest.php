<?php

declare(strict_types=1);

namespace Phauthentic\EventSourcing\Test\Aggregate\DomainEvent;

use Phauthentic\EventSourcing\DomainEvent\AggregateIdentityProvidingEventInterface;
use Phauthentic\EventSourcing\DomainEvent\AggregateVersionProvidingEvent;
use Phauthentic\EventSourcing\DomainEvent\TypeProvidingDomainEventInterface;
use PHPUnit\Framework\TestCase;
use Phauthentic\EventSourcing\DomainEvent\AbstractDomainEvent;

/**
 * A concrete implementation of AbstractDomainEvent for testing
 */
class ConcreteDomainEvent extends AbstractDomainEvent
{
    public function __construct(string $aggregateId, int $aggregateVersion, string $domainEventType)
    {
        $this->aggregateId = $aggregateId;
        $this->aggregateVersion = $aggregateVersion;
        $this->domainEventType = $domainEventType;
    }
}

/**
 *
 */
class AbstractDomainEventTest extends TestCase
{
    public function testGetAggregateId(): void
    {
        $event = new ConcreteDomainEvent('123', 1, 'TestEvent');
        $this->assertEquals('123', $event->getAggregateId());
    }

    public function testGetAggregateVersion(): void
    {
        $event = new ConcreteDomainEvent('123', 1, 'TestEvent');
        $this->assertEquals(1, $event->getAggregateVersion());
    }

    public function testGetEventType(): void
    {
        $event = new ConcreteDomainEvent('123', 1, 'TestEvent');
        $this->assertEquals('TestEvent', $event->getEventType());
    }

    public function testImplementsRequiredInterfaces(): void
    {
        $event = new ConcreteDomainEvent('123', 1, 'TestEvent');
        $this->assertInstanceOf(AggregateIdentityProvidingEventInterface::class, $event);
        $this->assertInstanceOf(AggregateVersionProvidingEvent::class, $event);
        $this->assertInstanceOf(TypeProvidingDomainEventInterface::class, $event);
    }
}
