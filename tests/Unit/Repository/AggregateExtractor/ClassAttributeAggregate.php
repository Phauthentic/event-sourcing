<?php

declare(strict_types=1);

namespace Phauthentic\EventSourcing\Test\Unit\Repository\AggregateExtractor;

use Phauthentic\EventSourcing\Aggregate\Attribute\EventSourcedAggregate;

/**
 *
 */
#[EventSourcedAggregate(
    versionProperty: 'aggregateVersion',
    identifierProperty: 'id',
    domainEventProperty: 'domainEvents',
    aggregateType: 'aggregateType'
)]
class ClassAttributeAggregate
{
    public function __construct(
        private string $id = 'ad9977c6-36fa-46ff-ba18-059ff3c608a4',
        private int $aggregateVersion = 0,
        private array $domainEvents = [],
        private ?string $aggregateType = 'Test.ClassAttributeAggregate'
    ) {
    }
}
