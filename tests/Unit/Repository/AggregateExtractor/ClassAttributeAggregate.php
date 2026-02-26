<?php

declare(strict_types=1);

namespace Phauthentic\EventSourcing\Test\Unit\Repository\AggregateExtractor;

use Phauthentic\EventSourcing\Aggregate\Attribute\EventSourcedAggregate;

#[EventSourcedAggregate(
    versionProperty: 'aggregateVersion',
    identifierProperty: 'id',
    domainEventProperty: 'domainEvents',
    aggregateType: 'aggregateType'
)]
class ClassAttributeAggregate
{
    public function __construct(
        // @phpstan-ignore-next-line
        private string $id = 'ad9977c6-36fa-46ff-ba18-059ff3c608a4',
        // @phpstan-ignore-next-line
        private int $aggregateVersion = 0,
        /** @var array<object> */
        // @phpstan-ignore-next-line
        private array $domainEvents = [],
        // @phpstan-ignore-next-line
        private ?string $aggregateType = 'Test.ClassAttributeAggregate'
    ) {
    }
}
