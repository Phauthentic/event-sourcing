<?php

declare(strict_types=1);

namespace Phauthentic\EventSourcing\Repository\AggregateFactory;

use Iterator;
use Phauthentic\SnapshotStore\SnapshotInterface;
use ReflectionClass;
use RuntimeException;

/**
 *
 */
class ReflectionFactory implements AggregateFactoryInterface
{
    /**
     * @param string $methodName
     * @param array<string, string> $classMap
     */
    public function __construct(
        protected readonly string $methodName = 'applyEventsFromHistory',
        protected readonly array $classMap = []
    ) {
    }

    public function reconstituteFromEvents(string|object $aggregate, Iterator $events): object
    {
        if ($aggregate instanceof SnapshotInterface) {
            $aggregate = $aggregate->getAggregateRoot();
            $aggregate->{$this->methodName}($events);

            return $aggregate;
        }

        if (is_string($aggregate)) {
            if (isset($this->classMap[$aggregate])) {
                $aggregate = $this->classMap[$aggregate];
            }

            /** @phpstan-ignore-next-line */
            $reflectionClass = new ReflectionClass($aggregate);
            $aggregate = $reflectionClass->newInstanceWithoutConstructor();
            $aggregate->{$this->methodName}($events);

            return $aggregate;
        }

        throw new RuntimeException(sprintf(
            'Could not reconstitute aggregate of type: %s',
            gettype($aggregate)
        ));
    }
}
