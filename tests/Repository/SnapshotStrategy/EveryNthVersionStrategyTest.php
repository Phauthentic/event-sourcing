<?php

declare(strict_types=1);

namespace Phauthentic\EventSourcing\Test\Repository\SnapshotStrategy;

use PHPUnit\Framework\TestCase;
use Phauthentic\EventSourcing\Repository\SnapshotStrategy\EveryNthVersionStrategy;
use Phauthentic\EventSourcing\Repository\AggregateDataInterface;

/**
 *
 */
class EveryNthVersionStrategyTest extends TestCase
{
    /**
     * @dataProvider versionDataProvider
     */
    public function testIsApplicable(int $modulus, int $version, bool $expected): void
    {
        $strategy = new EveryNthVersionStrategy($modulus);

        $aggregateData = $this->createMock(AggregateDataInterface::class);
        $aggregateData->method('getAggregateVersion')->willReturn($version);

        $result = $strategy->isApplicable($aggregateData);

        $this->assertEquals($expected, $result);
    }

    /**
     * @return array<string, array<mixed>>
     */
    public function versionDataProvider(): array
    {
        return [
            'Default modulus, applicable version' => [5, 10, true],
            'Default modulus, non-applicable version' => [5, 11, false],
            'Custom modulus, applicable version' => [3, 9, true],
            'Custom modulus, non-applicable version' => [3, 10, false],
            'Edge case: version 0' => [5, 0, true],
            'Edge case: version 1' => [5, 1, false],
        ];
    }

    public function testDefaultModulus(): void
    {
        $strategy = new EveryNthVersionStrategy();

        $aggregateData = $this->createMock(AggregateDataInterface::class);
        $aggregateData->method('getAggregateVersion')->willReturn(5);

        $result = $strategy->isApplicable($aggregateData);

        $this->assertTrue($result);
    }
}
