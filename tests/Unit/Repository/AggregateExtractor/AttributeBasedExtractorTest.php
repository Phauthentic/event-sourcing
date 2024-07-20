<?php

declare(strict_types=1);

namespace Phauthentic\EventSourcing\Test\Unit\Repository\AggregateExtractor;

use Example\Domain\Invoice\Invoice;
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
}
