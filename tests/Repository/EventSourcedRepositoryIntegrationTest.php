<?php

declare(strict_types=1);

namespace Phauthentic\EventSourcing\Test\Repository;

use Example\Domain\Invoice\Address;
use Example\Domain\Invoice\Invoice;
use Example\Domain\Invoice\InvoiceId;
use Example\Domain\Invoice\LineItem;
use Phauthentic\EventSourcing\Repository\AggregateExtractor\AggregateExtractorInterface;
use Phauthentic\EventSourcing\Repository\AggregateExtractor\AttributeBasedExtractor;
use Phauthentic\EventSourcing\Repository\AggregateFactory\AggregateFactoryInterface;
use Phauthentic\EventSourcing\Repository\AggregateFactory\ReflectionFactory;
use Phauthentic\EventSourcing\Repository\EventSourcedRepository;
use Phauthentic\EventStore\EventFactory;
use Phauthentic\EventStore\EventFactoryInterface;
use Phauthentic\EventStore\EventStoreInterface;
use Phauthentic\EventStore\InMemoryEventStore;
use Phauthentic\SnapshotStore\SnapshotFactory;
use Phauthentic\SnapshotStore\SnapshotFactoryInterface;
use Phauthentic\SnapshotStore\Store\NullStore;
use Phauthentic\SnapshotStore\Store\SnapshotStoreInterface;
use Phauthentic\SnapshotStore\Store\InMemorySnapshotStore;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class EventSourcedRepositoryIntegrationTest extends TestCase
{
    protected SnapshotStoreInterface $snapshotStore;
    protected EventStoreInterface $eventStore;
    protected AggregateExtractorInterface $extractor;
    protected AggregateFactoryInterface $aggregateFactory;
    protected EventFactoryInterface $eventFactory;
    protected SnapshotFactoryInterface $snapshotFactory;

    public function setUp(): void
    {
        parent::setUp();

        $this->snapshotStore = new InMemorySnapshotStore();
        $this->eventStore = new InMemoryEventStore();
        $this->extractor = new AttributeBasedExtractor();
        $this->aggregateFactory = new ReflectionFactory();
        $this->eventFactory = new EventFactory();
        $this->snapshotFactory = new SnapshotFactory();
    }

    protected function createRepository(): EventSourcedRepository
    {
        return new EventSourcedRepository(
            eventStore: $this->eventStore,
            aggregateExtractor: $this->extractor,
            aggregateFactory: $this->aggregateFactory,
            eventFactory: $this->eventFactory,
            snapshotStore: $this->snapshotStore,
            snapshotFactory: $this->snapshotFactory
        );
    }

    public function testEventSourcedRepositoryIntegration(): void
    {
        $aggregateId = Uuid::uuid4()->toString();
        $repository = $this->createRepository();

        // Act: Create the Invoice aggregate
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

        // Assert to verify its state
        $this->assertSame($aggregateId, (string)$invoice->getInvoiceId());
        $this->assertSame(2, $invoice->getDomainEventCount());
        $this->assertSame(1, $invoice->lineItemCount());

        // Act: Persist the aggregate and restore it
        $repository->persist($invoice);
        $invoice = $repository->restore($aggregateId, Invoice::class);

        // Assert
        $this->assertSame($aggregateId, (string)$invoice->getInvoiceId());
        $this->assertSame(1, $invoice->lineItemCount());
        $this->assertSame(2, $invoice->getAggregateVersion());

        // Act: Add one more line
        $invoice->addLineItem(LineItem::create(
            sku: '456',
            name: 'Book',
            price: 100.10
        ));
        $repository->persist($invoice);
        $invoice = $repository->restore($aggregateId, Invoice::class);

        // Assert
        $this->assertSame($aggregateId, (string)$invoice->getInvoiceId());
        $this->assertSame(2, $invoice->lineItemCount());
        $this->assertSame(3, $invoice->getAggregateVersion());

        // Act: Flag as Paid
        $invoice->flagAsPaid();
        $repository->persist($invoice);
        $invoice = $repository->restore($aggregateId, Invoice::class);

        // Assert
        $this->assertSame($aggregateId, (string)$invoice->getInvoiceId());
        $this->assertSame(4, $invoice->getAggregateVersion());
    }
}
