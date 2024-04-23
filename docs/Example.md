# Example

```php
<?php

declare(strict_types=1);

namespace Phauthentic\EventSourcing\Test\Repository;

use Example\Domain\Invoice\Address;
use Example\Domain\Invoice\Invoice;
use Example\Domain\Invoice\InvoiceId;
use Example\Domain\Invoice\LineItem;
use Phauthentic\EventSourcing\Repository\AggregateExtractor\AggregateExtractorInterface;
use Phauthentic\EventSourcing\Repository\AggregateExtractor\AttributeBasedExtractor;
use Phauthentic\EventSourcing\Repository\EventSourcedRepository;
use Phauthentic\EventStore\EventStoreInterface;
use Phauthentic\EventStore\InMemoryEventStore;
use Phauthentic\SnapshotStore\Store\SnapshotStoreInterface;
use Phauthentic\SnapshotStore\Store\InMemorySnapshotStore;
use PHPUnit\Framework\TestCase;

$repository = new EventSourcedRepository(
    eventStore: new InMemoryEventStore(),
    snapshotStore: new InMemorySnapshotStore(),
    aggregateExtractor: new AttributeBasedExtractor()
);


$aggregateId = '328f8a1a-f00c-482b-9fdf-05d88d9f6c6f';

// Create your aggregate
$invoice = Invoice::create(
    InvoiceId::fromString($aggregateId),
    Address::create(
        street: 'My Street',
        city: 'My City',
        zip: '121212'
    ),
    [
        LineItem::create(
            sku:'1',
            name: 'Beer',
            price: 12.10
        )
    ]
);

// Modify it and persist
$repository->persist($invoice);
$invoice = $repository->restore($aggregateId, Invoice::class);

// Modify it and persist
$invoice->addLineItem(new LineItem('456', 'Book', 100.10));
$repository->persist($invoice);
$invoice = $repository->restore($aggregateId, Invoice::class);

// Modify it and persist
$invoice->flagAsPaid();
$repository->persist($invoice);
$invoice = $repository->restore($aggregateId, Invoice::class);

var_dump($invoice);
```
