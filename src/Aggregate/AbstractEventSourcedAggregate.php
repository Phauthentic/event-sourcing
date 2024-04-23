<?php

declare(strict_types=1);

namespace Phauthentic\EventSourcing\Aggregate;

use Generator;
use Iterator;
use Phauthentic\EventSourcing\Aggregate\Exception\AggregateEventVersionMismatchException;
use Phauthentic\EventSourcing\Aggregate\Exception\EventMismatchException;
use Phauthentic\EventSourcing\Aggregate\Exception\MissingEventHandlerException;
use Phauthentic\EventSourcing\DomainEvent\AggregateIdentityProvidingEventInterface;

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

    protected bool $applyEventOnRecordThat = false;

    /**
     * Applies and records the event
     *
     * @param object $event
     * @return void
     * @throws EventMismatchException|MissingEventHandlerException
     */
    protected function recordThat(object $event): void
    {
        if ($this->applyEventOnRecordThat) {
            $this->applyEvent($event);
        } else {
            $this->aggregateVersion++;
        }

        $this->{$this->domainEventsProperty}[$this->aggregateVersion] = $event;
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
     * @param Iterator|array<int, object>|Generator $events
     * @return void
     * @throws EventMismatchException|AggregateEventVersionMismatchException|MissingEventHandlerException
     */
    public function applyEventsFromHistory(Iterator|array|Generator $events): void
    {
        foreach ($events as $event) {
            $this->assertNextVersion($event);
            $this->applyEvent($event->getPayload());
        }
    }

    /**
     * @param object $event
     * @return void
     * @throws AggregateEventVersionMismatchException
     */
    protected function assertNextVersion(object $event): void
    {
        if (
            $event instanceof AggregateIdentityProvidingEventInterface
            && $this->aggregateVersion + 1 !== $event->getAggregateVersion()
        ) {
            throw AggregateEventVersionMismatchException::fromVersions(
                $this->aggregateVersion,
                $event->getAggregateVersion()
            );
        }
    }
}
