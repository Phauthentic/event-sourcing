<?php

declare(strict_types=1);

namespace Phauthentic\EventSourcing\Test\Aggregate;

use Phauthentic\EventSourcing\Aggregate\AbstractEventSourcedAggregate;
use Phauthentic\EventSourcing\Aggregate\Attribute\DomainEvents;

/**
 *
 */
class ConcreteAggregate extends AbstractEventSourcedAggregate
{
    #[DomainEvents]
    protected array $aggregateEvents = [];

    public string $testProperty = '';

    public function __construct()
    {
        $this->aggregateId = 'test-id';
    }

    protected function whenTestEvent(TestEvent $event): void
    {
        $this->testProperty = $event->getText();
    }

    public function whenIdentityProvidingTestEvent(IdentityProvidingTestEvent $event)
    {
        $this->testProperty = $event->getText();
    }

    public function doSomething(string $data): void
    {
        $this->recordThat(new TestEvent($data));
    }

    public function getAggregateEvents(): array
    {
        return $this->aggregateEvents;
    }

    public function getAggregateVersion(): int
    {
        return $this->aggregateVersion;
    }
}
