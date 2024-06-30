<?php

declare(strict_types=1);

namespace Phauthentic\EventSourcing\Test\Repository\SnapshotStrategy;

use PHPUnit\Framework\TestCase;
use Phauthentic\EventSourcing\Repository\SnapshotStrategy\MultiStrategy;
use Phauthentic\EventSourcing\Repository\SnapshotStrategy\SnapshotStrategyInterface;
use Phauthentic\EventSourcing\Repository\AggregateDataInterface;

/**
 *
 */
class MultiStrategyTest extends TestCase
{
    public function testIsApplicableWithOneApplicableStrategy(): void
    {
        $aggregateData = $this->createMock(AggregateDataInterface::class);

        $strategy1 = $this->createMock(SnapshotStrategyInterface::class);
        $strategy1->method('isApplicable')->willReturn(false);

        $strategy2 = $this->createMock(SnapshotStrategyInterface::class);
        $strategy2->method('isApplicable')->willReturn(true);

        $strategy3 = $this->createMock(SnapshotStrategyInterface::class);
        $strategy3->method('isApplicable')->willReturn(false);

        $multiStrategy = new MultiStrategy([$strategy1, $strategy2, $strategy3]);

        $this->assertTrue($multiStrategy->isApplicable($aggregateData));
    }

    public function testIsApplicableWithNoApplicableStrategies(): void
    {
        $aggregateData = $this->createMock(AggregateDataInterface::class);

        $strategy1 = $this->createMock(SnapshotStrategyInterface::class);
        $strategy1->method('isApplicable')->willReturn(false);

        $strategy2 = $this->createMock(SnapshotStrategyInterface::class);
        $strategy2->method('isApplicable')->willReturn(false);

        $multiStrategy = new MultiStrategy([$strategy1, $strategy2]);

        $this->assertFalse($multiStrategy->isApplicable($aggregateData));
    }

    public function testIsApplicableWithEmptyStrategies(): void
    {
        $aggregateData = $this->createMock(AggregateDataInterface::class);

        $multiStrategy = new MultiStrategy([]);

        $this->assertFalse($multiStrategy->isApplicable($aggregateData));
    }

    public function testAddStrategy(): void
    {
        $aggregateData = $this->createMock(AggregateDataInterface::class);

        $strategy1 = $this->createMock(SnapshotStrategyInterface::class);
        $strategy1->method('isApplicable')->willReturn(false);

        $multiStrategy = new MultiStrategy([$strategy1]);

        $this->assertFalse($multiStrategy->isApplicable($aggregateData));

        $strategy2 = $this->createMock(SnapshotStrategyInterface::class);
        $strategy2->method('isApplicable')->willReturn(true);

        $reflectionClass = new \ReflectionClass(MultiStrategy::class);
        $addStrategyMethod = $reflectionClass->getMethod('addStrategy');
        $addStrategyMethod->setAccessible(true);
        $addStrategyMethod->invoke($multiStrategy, $strategy2);

        $this->assertTrue($multiStrategy->isApplicable($aggregateData));
    }
}
