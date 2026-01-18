<?php

declare(strict_types=1);

namespace Phauthentic\EventSourcing\Test\Unit\Repository\AggregateExtractor;

use Example\Domain\Invoice\Invoice;
use Phauthentic\EventSourcing\Aggregate\Attribute\AggregateIdentifier;
use Phauthentic\EventSourcing\Aggregate\Attribute\AggregateType;
use Phauthentic\EventSourcing\Aggregate\Attribute\AggregateVersion;
use Phauthentic\EventSourcing\Repository\AggregateExtractor\AttributeBasedExtractor;

/**
 *
 */
class AttributeBasedExtractorTest extends AbstractAggregateExtractorTest
{
    public function testEventSourcedAggregateAttributeBasedExtraction(): void
    {
        $aggregate = new ClassAttributeAggregate();
        $extractor = new AttributeBasedExtractor();

        $result = $extractor->extract($aggregate);

        $this->assertSame('ad9977c6-36fa-46ff-ba18-059ff3c608a4', $result->getAggregateId());
        $this->assertSame('Test.ClassAttributeAggregate', $result->getAggregateType());
        $this->assertSame(0, $result->getAggregateVersion());
        $this->assertCount(0, $result->getDomainEvents());
    }

    public function testPropertyAttributeBasedExtraction(): void
    {
        $aggregate = $this->getTestAggregate();

        $extractor = new AttributeBasedExtractor();
        $result = $extractor->extract($aggregate);

        $this->assertSame('ad9977c6-36fa-46ff-ba18-059ff3c608a4', $result->getAggregateId());
        $this->assertSame(Invoice::class, $result->getAggregateType());
        $this->assertSame(3, $result->getAggregateVersion());
        $this->assertCount(3, $result->getDomainEvents());
    }

    public function testExtractWithMissingAggregateId(): void
    {
        $aggregate = new class {
            public function applyEventsFromHistory(): void
            {
            }
        };

        $extractor = new AttributeBasedExtractor();

        $this->expectException(\Phauthentic\EventSourcing\Repository\AggregateExtractor\Exception\ExtractorException::class);
        $this->expectExceptionMessage('No property with the required attribute `Phauthentic\EventSourcing\Aggregate\Attribute\AggregateIdentifier` was found');

        $extractor->extract($aggregate);
    }

    public function testExtractWithMissingAggregateVersion(): void
    {
        $aggregate = new class {
            #[AggregateIdentifier]
            public string $aggregateId = 'test-id';
            public function applyEventsFromHistory(): void
            {
            }
        };

        $extractor = new AttributeBasedExtractor();

        $this->expectException(\Phauthentic\EventSourcing\Repository\AggregateExtractor\Exception\ExtractorException::class);
        $this->expectExceptionMessage('No property with the required attribute `Phauthentic\EventSourcing\Aggregate\Attribute\AggregateVersion` was found');

        $extractor->extract($aggregate);
    }

    public function testExtractWithMissingAggregateTypeAndVersion(): void
    {
        $aggregate = new class {
            #[AggregateIdentifier]
            public string $aggregateId = 'test-id';
            #[AggregateType]
            public string $aggregateType = 'TestType';
            public function applyEventsFromHistory(): void
            {
            }
        };

        $extractor = new AttributeBasedExtractor();

        $this->expectException(\Phauthentic\EventSourcing\Repository\AggregateExtractor\Exception\ExtractorException::class);
        $this->expectExceptionMessage('No property with the required attribute `Phauthentic\EventSourcing\Aggregate\Attribute\AggregateVersion` was found');

        $extractor->extract($aggregate);
    }

    public function testExtractWithMissingDomainEvents(): void
    {
        $aggregate = new class {
            #[AggregateIdentifier]
            public string $aggregateId = 'test-id';
            #[AggregateType]
            public string $aggregateType = 'TestType';
            #[AggregateVersion]
            public int $aggregateVersion = 1;
            public function applyEventsFromHistory(): void
            {
            }
        };

        $extractor = new AttributeBasedExtractor();

        $this->expectException(\Phauthentic\EventSourcing\Repository\AggregateExtractor\Exception\ExtractorException::class);
        $this->expectExceptionMessage('No property with the required attribute `Phauthentic\EventSourcing\Aggregate\Attribute\DomainEvents` was found');

        $extractor->extract($aggregate);
    }
}
