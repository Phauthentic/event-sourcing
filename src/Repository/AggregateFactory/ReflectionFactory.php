<?php

declare(strict_types=1);

namespace Phauthentic\EventSourcing\Repository\AggregateFactory;

use Iterator;
use Phauthentic\EventSourcing\Repository\EventSourcedRepositoryException;
use Phauthentic\SnapshotStore\SnapshotInterface;
use ReflectionClass;

/**
 *
 */
readonly class ReflectionFactory implements AggregateFactoryInterface
{
    /**
     * @param string $methodName
     * @param array<string, string> $classMap
     */
    public function __construct(
        protected string $methodName = 'applyEventsFromHistory',
        protected array  $classMap = []
    ) {
    }

    public function reconstituteFromEvents(string|object $aggregate, Iterator $events): object
    {
        if ($aggregate instanceof SnapshotInterface) {
            return $this->fromSnapshot($aggregate, $events);
        }

        if (is_string($aggregate)) {
            return $this->fromString($aggregate, $events);
        }

        throw EventSourcedRepositoryException::couldNotReconstituteAggregate($aggregate);
    }

    /**
     * @param object $aggregate
     * @param Iterator $events
     * @return mixed
     * @throws EventSourcedRepositoryException
     */
    protected function fromSnapshot(object $aggregate, Iterator $events)
    {
        $aggregate = $aggregate->getAggregateRoot();
        $this->assertAggregateHasMethod($aggregate);
        $aggregate->{$this->methodName}($events);

        return $aggregate;
    }

    /**
     * @param string $aggregate
     * @param Iterator $events
     * @return object|string
     * @throws EventSourcedRepositoryException
     * @throws \ReflectionException
     */
    protected function fromString(string $aggregate, Iterator $events) {
        if (isset($this->classMap[$aggregate])) {
            $aggregate = $this->classMap[$aggregate];
        }

        /** @phpstan-ignore-next-line */
        $reflectionClass = new ReflectionClass($aggregate);
        $aggregate = $reflectionClass->newInstanceWithoutConstructor();
        $this->assertAggregateHasMethod($aggregate);

        $aggregate->{$this->methodName}($events);

        return $aggregate;
    }

    protected function assertAggregateHasMethod(object $aggregate): void
    {
        if (!method_exists($aggregate, $this->methodName)) {
            throw new EventSourcedRepositoryException (sprintf(
                'Aggregate class `%s` does not have a method `%s` to reconstruct the aggregate state.',
                get_class($aggregate),
                $this->methodName
            ));
        }
    }
}
