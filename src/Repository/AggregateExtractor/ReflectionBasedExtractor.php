<?php

declare(strict_types=1);

namespace Phauthentic\EventSourcing\Repository\AggregateExtractor;

use DateTimeImmutable;
use Phauthentic\EventSourcing\Repository\AggregateData;
use Phauthentic\EventSourcing\Repository\AggregateDataInterface;
use Phauthentic\EventSourcing\Repository\AggregateExtractor\Exception\ExtractorException;
use Phauthentic\EventSourcing\Repository\AggregateExtractor\Exception\ReflectionPropertyExtractorException;
use Phauthentic\EventStore\Event;
use ReflectionClass;

/**
 * This extractor will get the information about the aggregate from its
 * properties using reflections.
 */
class ReflectionBasedExtractor implements AggregateExtractorInterface
{
    protected const AGGREGATE_EVENTS_PROPERTY = 'aggregateEvents';
    protected const AGGREGATE_IDENTIFIER_PROPERTY = 'aggregateId';
    protected const AGGREGATE_VERSION_PROPERTY = 'aggregateVersion';

    public function __construct(
        protected string $aggregateEventProperty = self::AGGREGATE_EVENTS_PROPERTY,
        protected string $aggregateVersionProperty = self::AGGREGATE_IDENTIFIER_PROPERTY,
        protected string $aggregateIdentifierProperty = self::AGGREGATE_VERSION_PROPERTY,
    ) {
    }

    /**
     * @param ReflectionClass $reflectionClass
     * @param string $propertyName
     * @return void
     * @throws ReflectionPropertyExtractorException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function assertAggregateProperty(ReflectionClass $reflectionClass, string $propertyName): void
    {
        if (!$reflectionClass->hasProperty($propertyName)) {
            throw ReflectionPropertyExtractorException::classHasMissingProperty(
                $reflectionClass->getName(),
                $propertyName
            );
        }
    }

    /**
     * @param mixed $value
     * @param string $name
     * @return void
     * @throws ExtractorException
     * @SuppressWarnings(PHPMD.StaticAccess)
 */
    protected function assertNotEmpty(mixed $value, string $name): void
    {
        if (empty($value)) {
            throw ExtractorException::notEmptyValue($name);
        }
    }

    /**
     * @throws \Phauthentic\EventSourcing\Repository\AggregateExtractor\Exception\ExtractorException
     */
    protected function assertAggregateProperties(ReflectionClass $reflectionClass): void
    {
        $this->assertAggregateProperty($reflectionClass, $this->aggregateEventProperty);
        $this->assertAggregateProperty($reflectionClass, $this->aggregateVersionProperty);
        $this->assertAggregateProperty($reflectionClass, $this->aggregateIdentifierProperty);
    }

    /**
     * @param ReflectionClass $reflectionClass
     * @param string $propertyName
     * @param object $object
     * @return mixed
     * @throws \ReflectionException
     */
    protected function getPropertyFromAggregate(
        ReflectionClass $reflectionClass,
        string $propertyName,
        object $object
    ): mixed {
        return $reflectionClass
            ->getProperty($propertyName)
            ->getValue($object);
    }

    /**
     * @param ReflectionClass $reflectionClass
     * @param object $aggregate
     * @return array<int, object>
     */
    protected function extractEvents(ReflectionClass $reflectionClass, object $aggregate): array
    {
        return (array)$this->getPropertyFromAggregate(
            $reflectionClass,
            $this->aggregateEventProperty,
            $aggregate
        );
    }

    protected function extractAggregateId(ReflectionClass $reflectionClass, object $aggregate): string
    {
        return (string)$this->getPropertyFromAggregate(
            $reflectionClass,
            $this->aggregateVersionProperty,
            $aggregate
        );
    }

    protected function extractAggregateVersion(ReflectionClass $reflectionClass, object $aggregate): int
    {
        return (int)$this->getPropertyFromAggregate(
            $reflectionClass,
            $this->aggregateIdentifierProperty,
            $aggregate
        );
    }

    protected function resetEvents(ReflectionClass $reflectionClass, object $aggregate): void
    {
        $property = $reflectionClass->getProperty($this->aggregateEventProperty);
        $property->setValue($aggregate, []);
    }

    /**
     * @throws \Phauthentic\EventSourcing\Repository\AggregateExtractor\Exception\ExtractorException
     */
    public function extract(object $aggregate): AggregateDataInterface
    {
        $reflectionClass = new ReflectionClass($aggregate);
        $this->assertAggregateProperties($reflectionClass);

        $events = $this->extractEvents($reflectionClass, $aggregate);
        $aggregateId = $this->extractAggregateId($reflectionClass, $aggregate);
        $aggregateVersion = $this->extractAggregateVersion($reflectionClass, $aggregate);
        $this->resetEvents($reflectionClass, $aggregate);

        $this->assertNotEmpty($aggregateId, 'Aggregate ID');
        $this->assertNotEmpty($aggregateVersion, 'Aggregate Version');

        $storeEvents = [];
        foreach ($events as $version => $event) {
            $storeEvents[] = new Event(
                aggregateId: $aggregateId,
                aggregateVersion: $version,
                event: get_class($event),
                payload: $event,
                createdAt: (new DateTimeImmutable())
            );
        }

        return new AggregateData(
            $aggregateId,
            get_class($aggregate),
            $aggregateVersion,
            $storeEvents
        );
    }
}
