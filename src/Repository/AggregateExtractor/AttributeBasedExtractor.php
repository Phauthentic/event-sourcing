<?php

declare(strict_types=1);

namespace Phauthentic\EventSourcing\Repository\AggregateExtractor;

use Phauthentic\EventSourcing\Aggregate\Attribute\DomainEvents;
use Phauthentic\EventSourcing\Aggregate\Attribute\EventSourcedAggregate;
use Phauthentic\EventSourcing\Aggregate\Attribute\AggregateIdentifier;
use Phauthentic\EventSourcing\Aggregate\Attribute\AggregateType;
use Phauthentic\EventSourcing\Aggregate\Attribute\AggregateVersion;
use Phauthentic\EventSourcing\Repository\AggregateData;
use Phauthentic\EventSourcing\Repository\AggregateDataInterface;
use Phauthentic\EventSourcing\Repository\AggregateExtractor\Exception\ExtractorException;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionProperty;

/**
 *
 */
class AttributeBasedExtractor implements AggregateExtractorInterface
{
    public function extract(object $aggregate): AggregateDataInterface
    {
        $reflectionClass = new ReflectionClass($aggregate);

        $attributes = $reflectionClass->getAttributes(EventSourcedAggregate::class);
        if (!empty($attributes)) {
            return $this->extractAggregate($reflectionClass, $aggregate);
        }

        $aggregateId = $this->extractAggregateId($reflectionClass, $aggregate);
        $aggregateType = $this->extractAggregateType($reflectionClass, $aggregate);
        $aggregateVersion = $this->extractAggregateVersion($reflectionClass, $aggregate);
        $aggregateEvents = $this->extractAggregateEvents($reflectionClass, $aggregate);

        return new AggregateData(
            aggregateId: $aggregateId,
            aggregateType: $aggregateType,
            version: $aggregateVersion,
            events: $aggregateEvents
        );
    }

    protected function assertPropertyHasName(ReflectionClass $reflectionClass, string $name): void
    {
        if (!$reflectionClass->hasProperty($name)) {
            throw new ExtractorException(sprintf(
                'Property %s not found in %s',
                $name,
                $reflectionClass->getName()
            ));
        }
    }

    protected function extractAggregateTypeFromAggregate(
        object $aggregate,
        EventSourcedAggregate $aggregateAttribute,
        ReflectionClass $reflectionClass
    ): string {
        $aggregateType = get_class($aggregate);
        if ($aggregateAttribute->aggregateType !== null) {
            $aggregateType = $reflectionClass
                ->getProperty($aggregateAttribute->aggregateType)
                ->getValue($aggregate);
        }

        return $aggregateType;
    }

    /**
     * @param ReflectionClass $reflectionClass
     *
     * @return array<ReflectionAttribute>
     */
    protected function getAttributes(ReflectionClass $reflectionClass): array
    {
        return $reflectionClass->getAttributes(EventSourcedAggregate::class);
    }

    protected function assertAggregateHasAttributes(ReflectionClass $reflectionClass): void
    {
        $attributes = $reflectionClass->getAttributes(EventSourcedAggregate::class);

        if (empty($attributes)) {
            throw new ExtractorException(sprintf(
                'Attribute `%s` found in `%s`',
                EventSourcedAggregate::class,
                $reflectionClass->getName()
            ));
        }
    }

    protected function extractAggregate(ReflectionClass $reflectionClass, object $aggregate): AggregateData
    {
        $this->assertAggregateHasAttributes($reflectionClass);
        $attributes = $this->getAttributes($reflectionClass);

        /** @var EventSourcedAggregate $aggregateAttribute */
        $aggregateAttribute = $attributes[0]->newInstance();
        $properties = [
            'identifierProperty' => $aggregateAttribute->identifierProperty,
            'versionProperty' => $aggregateAttribute->versionProperty,
            'domainEventProperty' => $aggregateAttribute->domainEventProperty
        ];

        foreach ($properties as $name) {
            $this->assertPropertyHasName($reflectionClass, $name);
        }

        $aggregateType = $this->extractAggregateTypeFromAggregate($aggregate, $aggregateAttribute, $reflectionClass);

        return new AggregateData(
            aggregateId: (string)$reflectionClass
                ->getProperty($aggregateAttribute->identifierProperty)
                ->getValue($aggregate),
            aggregateType: $aggregateType,
            version: (int)$reflectionClass
                ->getProperty($aggregateAttribute->versionProperty)
                ->getValue($aggregate),
            events: $reflectionClass
                ->getProperty($aggregateAttribute->domainEventProperty)
                ->getValue($aggregate)
        );
    }

    protected function extractAggregateId(ReflectionClass $reflectionClass, object $aggregate): string
    {
        $property = $this->findPropertyWithRequiredAttribute($reflectionClass, AggregateIdentifier::class);

        return (string)$this->getValueFromAttribute($property, AggregateIdentifier::class, $aggregate);
    }

    protected function extractAggregateType(ReflectionClass $reflectionClass, object $aggregate): string
    {
        $property = $this->findPropertyWithAttribute($reflectionClass, AggregateType::class);

        return $property
            ? (string)$this->getValueFromAttribute($property, AggregateType::class, $aggregate)
            : get_class($aggregate);
    }

    protected function extractAggregateVersion(ReflectionClass $reflectionClass, object $aggregate): int
    {
        $property = $this->findPropertyWithRequiredAttribute($reflectionClass, AggregateVersion::class);
        $value = $this->getValueFromAttribute($property, AggregateVersion::class, $aggregate);

        if (!is_int($value)) {
            throw new ExtractorException(sprintf(
                'The version property must be an integer, `%s` given.',
                gettype($value)
            ));
        }

        return $value;
    }

    /**
     * @param ReflectionClass $reflectionClass
     * @param object $aggregate
     * @return array<int, object>
     */
    protected function extractAggregateEvents(ReflectionClass $reflectionClass, object $aggregate): array
    {
        $property = $this->findPropertyWithRequiredAttribute($reflectionClass, DomainEvents::class);
        $value = $this->getValueFromAttribute($property, DomainEvents::class, $aggregate);

        if (!is_array($value)) {
            throw new ExtractorException(sprintf(
                'The version property must be an integer, `%s` given.',
                gettype($value)
            ));
        }

        return $value;
    }

    protected function findPropertyWithAttribute(
        ReflectionClass $reflectionClass,
        string $attribute
    ): ?ReflectionProperty {
        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            if ($this->propertyHasAttribute($reflectionProperty, $attribute)) {
                return $reflectionProperty;
            }
        }

        return null;
    }

    /**
     * @param ReflectionClass $reflectionClass
     * @param string $attribute
     * @return ReflectionProperty
     */
    protected function findPropertyWithRequiredAttribute(
        ReflectionClass $reflectionClass,
        string $attribute
    ): ReflectionProperty {
        $reflectionProperty = $this->findPropertyWithAttribute($reflectionClass, $attribute);
        if ($reflectionProperty) {
            return $reflectionProperty;
        }

        throw new ExtractorException(sprintf(
            'No property with the required attribute `%s` was found class `%s`.',
            $attribute,
            $reflectionClass->getName()
        ));
    }

    /**
     * @param $property
     * @param $attribute
     * @return bool
     */
    protected function propertyHasAttribute(ReflectionProperty $property, string $attribute): bool
    {
        return isset($property->getAttributes($attribute)[0]);
    }

    /**
     * @param $property
     * @param $attribute
     * @return mixed
     */
    protected function getValueFromAttribute(ReflectionProperty $property, string $attribute, object $object): mixed
    {
        $attributes = $property->getAttributes($attribute);
        if (isset($attributes[0])) {
            return $property->getValue($object);
        }

        return null;
    }
}
