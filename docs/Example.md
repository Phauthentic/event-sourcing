# Event Sourcing Usage Example

This example shows the usage of event sourcing and this library with the example code provided as part of this repository in `examples/`.

```php
<?php

declare(strict_types=1);

namespace Example;

use Example\Domain\Invoice\Address;
use Example\Domain\Invoice\Invoice;
use Example\Domain\Invoice\InvoiceId;
use Example\Domain\Invoice\LineItem;
use Phauthentic\EventSourcing\Repository\AggregateExtractor\AttributeBasedExtractor;
use Phauthentic\EventSourcing\Repository\AggregateFactory\ReflectionFactory;
use Phauthentic\EventSourcing\Repository\EventSourcedRepository;
use Phauthentic\EventStore\EventFactory;
use Phauthentic\EventStore\InMemoryEventStore;
use Phauthentic\SnapshotStore\SnapshotFactory;
use Phauthentic\SnapshotStore\Store\InMemorySnapshotStore;

$repository = new EventSourcedRepository(
    eventStore: new InMemoryEventStore(),
    aggregateExtractor: new AttributeBasedExtractor(),
    aggregateFactory: new ReflectionFactory(),
    eventFactory: new EventFactory(),
    snapshotStore: new InMemorySnapshotStore(),
    snapshotFactory: new SnapshotFactory()
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
            sku: '1',
            name: 'Beer',
            price: 12.10
        )
    ]
);

// Modify it and persist
$repository->persist($invoice);
$invoice = $repository->restore($aggregateId, Invoice::class);

// Modify it and persist
$invoice->addLineItem(LineItem::create('456', 'Book', 100.10));
$repository->persist($invoice);
$invoice = $repository->restore($aggregateId, Invoice::class);

// Modify it and persist
$invoice->flagAsPaid();
$repository->persist($invoice);
$invoice = $repository->restore($aggregateId, Invoice::class);

var_dump($invoice);
```
