<?php

declare(strict_types=1);

namespace Phauthentic\EventSourcing\Test\Repository\AggregateExtractor;

use Example\Domain\Invoice\Invoice;
use Phauthentic\EventSourcing\Repository\AggregateExtractor\ReflectionBasedExtractor;

class ReflectionPropertyExtractorTest extends AbstractAggregateExtractorTest
{
    public function testExtract(): void
    {
        $invoice = $this->getTestAggregate();

        $extractor = new ReflectionBasedExtractor();
        $result = $extractor->extract($invoice);

        $this->assertSame('ad9977c6-36fa-46ff-ba18-059ff3c608a4', $result->getAggregateId());
        $this->assertSame(Invoice::class, $result->getAggregateType());
        $this->assertSame(3, $result->getAggregateVersion());
    }
}
