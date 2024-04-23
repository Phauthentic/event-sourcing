<?php

declare(strict_types=1);

namespace Phauthentic\EventSourcing\Aggregate\Attribute;

use Attribute;

/**
 *
 */
#[Attribute(Attribute::TARGET_CLASS)]
class EventSourcedAggregate
{
    public function __construct(
        public string $versionProperty = 'aggregateVersion',
        public string $identifierProperty = 'id',
        public string $domainEventProperty = 'domainEvents',
        public ?string $aggregateType = null
    ) {
    }
}
