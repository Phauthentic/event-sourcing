<?php

declare(strict_types=1);

namespace Phauthentic\EventSourcing\Repository;

use Phauthentic\EventSourcing\EventSourcingException;

/**
 *
 */
class EventSourcedRepositoryException extends EventSourcingException
{
    public static function couldNotReconstituteAggregate(string $aggregateType): self
    {
        return new self(sprintf(
            'Could not reconstitute aggregate of type: %s',
            $aggregateType
        ));
    }

    public static function missingReconstitutionMethod(string $aggregateType, string $methodName): self
    {
        return new self(sprintf(
            'Aggregate class `%s` does not have a method `%s` to reconstruct the aggregate state.',
            $aggregateType,
            $methodName
        ));
    }
}
