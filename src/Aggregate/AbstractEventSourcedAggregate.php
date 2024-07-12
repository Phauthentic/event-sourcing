<?php

declare(strict_types=1);

namespace Phauthentic\EventSourcing\Aggregate;

use Generator;
use Iterator;
use Phauthentic\EventSourcing\Aggregate\Attribute\DomainEvents;
use Phauthentic\EventSourcing\Aggregate\Exception\AggregateEventVersionMismatchException;
use Phauthentic\EventSourcing\Aggregate\Exception\AggregateException;
use Phauthentic\EventSourcing\Aggregate\Exception\EventMismatchException;
use Phauthentic\EventSourcing\Aggregate\Exception\MissingEventHandlerException;
use Phauthentic\EventSourcing\DomainEvent\AggregateIdentityProvidingEventInterface;
use Phauthentic\EventStore\EventInterface;
use ReflectionClass;
use ReflectionProperty;

/**
 *
 */
abstract class AbstractEventSourcedAggregate
{
    protected string $aggregateId = '';

    /**
     * @var array<int, object>
     */
    protected array $aggregateEvents = [];

    protected string $domainEventsProperty = 'aggregateEvents';

    protected int $aggregateVersion = 0;

    protected const EVENT_METHOD_PREFIX = 'when';

    protected const EVENT_METHOD_SUFFIX = '';

    /**
     * Applies and records the event
     *
     * @param object $event
     * @return void
     * @throws EventMismatchException|MissingEventHandlerException|AggregateException
     */
    protected function recordThat(object $event): void
    {
        $reflectionClass = new ReflectionClass($this);
        $domainEventsProperty = $this->findDomainEventsProperty($reflectionClass);

        if ($domainEventsProperty->isPrivate()) {
            $domainEventsProperty->setAccessible(true);
        }

        $events = $domainEventsProperty->getValue($this);
        $events[] = $event;
        $domainEventsProperty->setValue($this, $events);
        $this->aggregateVersion++;
    }

    protected function findDomainEventsProperty(ReflectionClass $reflectionClass): ReflectionProperty
    {
        foreach ($reflectionClass->getProperties() as $property) {
            if (!empty($property->getAttributes(DomainEvents::class))) {
                return $property;
            }
        }

        throw new AggregateException(sprintf(
            'Could not find domain events property %s',
            $this->domainEventsProperty
        ));
    }

    protected function getEventNameFromEvent(object $event): string
    {
        $eventClass = get_class($event);
        $eventName = ucfirst(substr($eventClass, strrpos($eventClass, '\\') + 1));

        if (!empty(static::EVENT_METHOD_SUFFIX)) {
            $eventName = substr($eventName, 0, -strlen(static::EVENT_METHOD_SUFFIX));
        }

        return static::EVENT_METHOD_PREFIX . $eventName;
    }

    /**
     * @param object $event
     * @param string $eventName
     * @return void
     * @throws MissingEventHandlerException
     * @@SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function assertEventHandlerExists(object $event, string $eventName): void
    {
        if (method_exists($this, $eventName)) {
            return;
        }

        throw MissingEventHandlerException::withNameAndClass(
            $eventName,
            get_class($event),
            get_class($this)
        );
    }

    /**
     * @param object $event
     * @return void
     * @throws EventMismatchException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function assertEventMatchesAggregate(object $event): void
    {
        if (
            $event instanceof AggregateIdentityProvidingEventInterface
            && $this->aggregateId !== $event->getAggregateId()
        ) {
            throw EventMismatchException::eventDoesNotMatchAggregateWith(
                $event,
                $this,
            );
        }
    }

    /**
     * @param object $event
     * @return void
     * @throws EventMismatchException|MissingEventHandlerException
     */
    protected function applyEvent(object $event): void
    {
        $eventName = $this->getEventNameFromEvent($event);

        $this->assertEventHandlerExists($event, $eventName);
        $this->assertEventMatchesAggregate($event);

        $this->{$eventName}($event);
        $this->aggregateVersion++;
    }

    /**
     * @param Iterator<int, EventInterface>|array<int, EventInterface>|Generator<int, EventInterface> $events
     * @return void
     * @throws EventMismatchException|AggregateEventVersionMismatchException|MissingEventHandlerException
     */
    public function applyEventsFromHistory(Iterator|array|Generator $events): void
    {
        /** @var EventInterface $event */
        foreach ($events as $event) {
            $this->assertNextVersion($event->getAggregateVersion());
            $this->applyEvent($event->getPayload());
        }
    }

    /**
     * @param int $eventVersion
     * @return void
     * @throws AggregateEventVersionMismatchException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function assertNextVersion(int $eventVersion): void
    {
        if ($this->aggregateVersion + 1 === $eventVersion) {
            return;
        }

        throw AggregateEventVersionMismatchException::fromVersions(
            $this->aggregateVersion,
            $eventVersion
        );
    }
}
