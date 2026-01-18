<?php

declare(strict_types=1);

namespace Phauthentic\EventSourcing\Test\Unit\Repository;

use Phauthentic\EventSourcing\Repository\EventSourcedRepositoryException;
use PHPUnit\Framework\TestCase;

/**
 *
 */
class EventSourcedRepositoryExceptionTest extends TestCase
{
    public function testCouldNotReconstituteAggregate(): void
    {
        $exception = EventSourcedRepositoryException::couldNotReconstituteAggregate('TestAggregate');

        $this->assertInstanceOf(EventSourcedRepositoryException::class, $exception);
        $this->assertEquals('Could not reconstitute aggregate of type: TestAggregate', $exception->getMessage());
    }

    public function testMissingReconstitutionMethod(): void
    {
        $exception = EventSourcedRepositoryException::missingReconstitutionMethod('TestAggregate', 'applyEventsFromHistory');

        $this->assertInstanceOf(EventSourcedRepositoryException::class, $exception);
        $this->assertEquals('Aggregate class `TestAggregate` does not have a method `applyEventsFromHistory` to reconstruct the aggregate state.', $exception->getMessage());
    }
}
