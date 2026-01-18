<?php

declare(strict_types=1);

namespace Phauthentic\EventSourcing\Test\Unit\Repository\AggregateExtractor;

use Example\Domain\Invoice\Address;
use Example\Domain\Invoice\Invoice;
use Example\Domain\Invoice\InvoiceId;
use Example\Domain\Invoice\LineItem;
use PHPUnit\Framework\TestCase;

/**
 *
 */
abstract class AbstractAggregateExtractorTest extends TestCase
{
    protected function getTestAggregate(): Invoice
    {
        return Invoice::create(
            InvoiceId::fromString('ad9977c6-36fa-46ff-ba18-059ff3c608a4'),
            new Address(
                street: 'Victory Avenue',
                city: 'Kiev',
                zip: '666'
            ),
            [
                new LineItem('1', 'Foo', 12.5),
                new LineItem('2', 'Foo', 12.5)
            ]
        );
    }
}
