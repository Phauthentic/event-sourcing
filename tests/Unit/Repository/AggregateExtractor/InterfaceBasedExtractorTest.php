<?php

declare(strict_types=1);

namespace Phauthentic\EventSourcing\Test\Unit\Repository\AggregateExtractor;

use Phauthentic\EventSourcing\Repository\AggregateData;
use Phauthentic\EventSourcing\Repository\AggregateExtractor\Exception\ExtractorException;
use Phauthentic\EventSourcing\Repository\AggregateExtractor\InterfaceBasedExtractor;
use PHPUnit\Framework\TestCase;

/**
 *
 */
class InterfaceBasedExtractorTest extends TestCase
{
    private InterfaceBasedExtractor $extractor;

    protected function setUp(): void
    {
        $this->extractor = new InterfaceBasedExtractor();
    }

    public function testExtractWithValidAggregate(): void
    {
        $aggregate = new AggregateWithInterfaces(
            aggregateId: '123',
            aggregateVersion: 1,
            aggregateType: 'AggregateWithInterfaces',
            events: []
        );

        $result = $this->extractor->extract($aggregate);

        $this->assertInstanceOf(AggregateData::class, $result);
        $this->assertEquals('123', $result->getAggregateId());
        $this->assertEquals(1, $result->getAggregateVersion());
        $this->assertEquals([], $result->getDomainEvents());
        $this->assertEquals('AggregateWithInterfaces', $result->getAggregateType());
    }

    public function testExtractWithTypeProvidingAggregate(): void
    {
        $aggregate = new AggregateWithInterfaces(
            aggregateId: '456',
            aggregateVersion: 2,
            aggregateType: 'AggregateWithInterfacesTest',
            events: []
        );

        $result = $this->extractor->extract($aggregate);

        $this->assertInstanceOf(AggregateData::class, $result);
        $this->assertEquals('456', $result->getAggregateId());
        $this->assertEquals(2, $result->getAggregateVersion());
        $this->assertEquals([], $result->getDomainEvents());
        $this->assertEquals('AggregateWithInterfacesTest', $result->getAggregateType());
    }

    public function testExtractWithInvalidAggregate(): void
    {
        $invalidAggregate = new \stdClass();

        $this->expectException(ExtractorException::class);
        $this->extractor->extract($invalidAggregate);
    }

    public function testExtractWithNonTypeProvidingAggregate(): void
    {
        // Create an aggregate that implements EventSourcedAggregateInterface but not TypeProvidingAggregateInterface
        $aggregate = new class implements \Phauthentic\EventSourcing\Aggregate\EventSourcedAggregateInterface {
            public function getAggregateId(): string
            {
                return '789';
            }
            public function getAggregateVersion(): int
            {
                return 3;
            }
            public function consumeAggregateEvents(): array
            {
                return [];
            }
        };

        $result = $this->extractor->extract($aggregate);

        $this->assertInstanceOf(AggregateData::class, $result);
        $this->assertEquals('789', $result->getAggregateId());
        $this->assertEquals(3, $result->getAggregateVersion());
        $this->assertEquals([], $result->getDomainEvents());
        // Should use get_class() as fallback for type
        $this->assertStringContainsString('InterfaceBasedExtractorTest', $result->getAggregateType());
    }
}
