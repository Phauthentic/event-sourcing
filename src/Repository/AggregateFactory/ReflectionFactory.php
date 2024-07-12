<?php

declare(strict_types=1);

namespace Phauthentic\EventSourcing\Repository\AggregateFactory;

use Iterator;
use Phauthentic\EventSourcing\Repository\EventSourcedRepositoryException;
use Phauthentic\SnapshotStore\SnapshotInterface;
use ReflectionClass;
use ReflectionException;

/**
 *
 */
readonly class ReflectionFactory implements AggregateFactoryInterface
{
    /**
     * @param string $applyEventsMethodName
     * @param array<string, string> $classMap
     */
    public function __construct(
        protected string $applyEventsMethodName = 'applyEventsFromHistory',
        protected array $classMap = []
    ) {
    }

    /**
     * @param string|object $aggregate
     * @param Iterator $events
     * @return object
     * @throws EventSourcedRepositoryException
     * @throws ReflectionException
     * @SuppressWarnings(PHPMD.StaticAccess)
 */
    public function reconstituteFromEvents(string|object $aggregate, Iterator $events): object
    {
        if (is_string($aggregate)) {
            return $this->fromString($aggregate, $events);
        }

        if ($aggregate instanceof SnapshotInterface) {
            return $this->fromSnapshot($aggregate, $events);
        }

        $this->assertAggregateHasMethod($aggregate);
        /*
        foreach ($events as $event) {
            //var_dump($event);
        }
        */
        $aggregate->{$this->applyEventsMethodName}($events);

        return $aggregate;
    }

    /**
     * @param SnapshotInterface $snapshot
     * @param Iterator $events
     * @return mixed
     * @throws EventSourcedRepositoryException
     */
    protected function fromSnapshot(SnapshotInterface $snapshot, Iterator $events)
    {
        $aggregate = $snapshot->getAggregateRoot();
        $this->assertAggregateHasMethod($aggregate);
        $aggregate->{$this->applyEventsMethodName}($events);

        return $aggregate;
    }

    /**
     * @param string $aggregate
     * @param Iterator $events
     * @return object
     * @throws EventSourcedRepositoryException
     * @throws ReflectionException
     */
    protected function fromString(string $aggregate, Iterator $events): object
    {
        if (isset($this->classMap[$aggregate])) {
            $aggregate = $this->classMap[$aggregate];
        }

        /** @phpstan-ignore-next-line */
        $reflectionClass = new ReflectionClass($aggregate);
        $aggregate = $reflectionClass->newInstanceWithoutConstructor();
        $this->assertAggregateHasMethod($aggregate);

        $aggregate->{$this->applyEventsMethodName}($events);

        return $aggregate;
    }

    protected function assertAggregateHasMethod(object $aggregate): void
    {
        if (!method_exists($aggregate, $this->applyEventsMethodName)) {
            throw  EventSourcedRepositoryException::missingReconstitutionMethod(
                get_class($aggregate),
                $this->applyEventsMethodName
            );
        }
    }
}
