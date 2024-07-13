<?php

declare(strict_types=1);

namespace Phauthentic\EventSourcing\Test\Repository;

use Example\Domain\Invoice\Address;
use Example\Domain\Invoice\Event\InvoiceCreated;
use Example\Domain\Invoice\Event\LineItemAdded;
use Example\Domain\Invoice\Invoice;
use Example\Domain\Invoice\InvoiceId;
use Example\Domain\Invoice\LineItem;
use Phauthentic\EventSourcing\Repository\AggregateExtractor\AggregateExtractorInterface;
use Phauthentic\EventSourcing\Repository\AggregateExtractor\AttributeBasedExtractor;
use Phauthentic\EventSourcing\Repository\AggregateFactory\AggregateFactoryInterface;
use Phauthentic\EventSourcing\Repository\AggregateFactory\ReflectionFactory;
use Phauthentic\EventSourcing\Repository\EventSourcedRepository;
use Phauthentic\EventSourcing\Repository\EventSourcedRepositoryInterface;
use Phauthentic\EventSourcing\Repository\SnapshotStrategy\EveryNthVersionStrategy;
use Phauthentic\EventSourcing\Repository\SnapshotStrategy\EveryVersionStrategy;
use Phauthentic\EventStore\EventFactory;
use Phauthentic\EventStore\EventFactoryInterface;
use Phauthentic\EventStore\EventStoreInterface;
use Phauthentic\EventStore\InMemoryEventStore;
use Phauthentic\SnapshotStore\SnapshotFactory;
use Phauthentic\SnapshotStore\SnapshotFactoryInterface;
use Phauthentic\SnapshotStore\Store\SnapshotStoreInterface;
use Phauthentic\SnapshotStore\Store\InMemorySnapshotStore;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 *
 */
class EventSourcedRepositoryIntegrationTest extends TestCase
{
    protected SnapshotStoreInterface $snapshotStore;
    protected EventStoreInterface $eventStore;
    protected AggregateExtractorInterface $extractor;
    protected AggregateFactoryInterface $aggregateFactory;
    protected EventFactoryInterface $eventFactory;
    protected SnapshotFactoryInterface $snapshotFactory;

    protected string $aggregateId = '';

    protected EventSourcedRepositoryInterface $repository;

    public function setUp(): void
    {
        parent::setUp();

        $this->snapshotStore = new InMemorySnapshotStore();
        $this->eventStore = new InMemoryEventStore();
        $this->extractor = new AttributeBasedExtractor();
        $this->aggregateFactory = new ReflectionFactory();
        $this->eventFactory = new EventFactory();
        $this->snapshotFactory = new SnapshotFactory();

        $this->aggregateId = Uuid::uuid4()->toString();
        $this->repository = $this->createRepository();
    }

    protected function createRepository(): EventSourcedRepository
    {
        return new EventSourcedRepository(
            eventStore: $this->eventStore,
            aggregateExtractor: $this->extractor,
            aggregateFactory: $this->aggregateFactory,
            eventFactory: $this->eventFactory,
            snapshotStore: $this->snapshotStore,
            snapshotFactory: $this->snapshotFactory,
            snapshotStrategies: [
                new EveryVersionStrategy(),
            ]
        );
    }

    private function createInvoice(): Invoice
    {
        // Act: Create the Invoice aggregate
        $invoice = Invoice::create(
            InvoiceId::fromString($this->aggregateId),
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

        // Asserts to verify its state
        $this->assertSame($this->aggregateId, (string)$invoice->getInvoiceId());
        $this->assertSame(2, $invoice->getDomainEventCount());
        $this->assertSame(2, $invoice->getAggregateVersion());
        $this->assertSame(1, $invoice->lineItemCount());

        $this->assertInstanceOf(InvoiceCreated::class, $invoice->getDomainEvents()[0]);
        $this->assertInstanceOf(LineItemAdded::class, $invoice->getDomainEvents()[1]);

        return $invoice;
    }

    private function assertAggregateState(
        Invoice $invoice,
        int $expectedDomainEventCount,
        int $expectedAggregateVersion,
        int $expectedLineItemCount
    ) {
        $this->assertSame($expectedDomainEventCount, $invoice->getDomainEventCount());
        $this->assertSame($expectedAggregateVersion, $invoice->getAggregateVersion());
        $this->assertSame($expectedLineItemCount, $invoice->lineItemCount());
    }

    private function persistAndRestoreTheAggregateFirstTime(Invoice $invoice): Invoice
    {
        // Act: Restore the aggregate
        $this->repository->persist($invoice);

        $this->assertAggregateState(
            invoice: $invoice,
            expectedDomainEventCount: 0,
            expectedAggregateVersion: 2,
            expectedLineItemCount: 1
        );

        // Act: Restore the aggregate
        $invoice = $this->repository->restore($this->aggregateId, Invoice::class);

        // Assert
        $this->assertAggregateState(
            invoice: $invoice,
            expectedDomainEventCount: 0,
            expectedAggregateVersion: 2,
            expectedLineItemCount: 1
        );

        return $invoice;
    }

    private function addLineItemAndAssertAggregateState(Invoice $invoice): Invoice
    {
        // Act: Add one more line
        $invoice->addLineItem(LineItem::create(
            sku: '456',
            name: 'Book',
            price: 100.10
        ));

        // Assert that the aggregate should have now 2 line items
        $this->assertAggregateState(
            invoice: $invoice,
            expectedDomainEventCount: 1,
            expectedAggregateVersion: 3,
            expectedLineItemCount: 2
        );

        return $invoice;
    }

    private function persistAndRestoreAggregateSecondTime(Invoice $invoice): Invoice
    {
        // Act: Persist the aggregate and restore it
        $this->repository->persist($invoice);
        $invoice = $this->repository->restore($this->aggregateId, Invoice::class);

        // Check that after restoring, the aggregates properties are still the same
        $this->assertAggregateState(
            invoice: $invoice,
            expectedDomainEventCount: 0,
            expectedAggregateVersion: 3,
            expectedLineItemCount: 2
        );

        return $invoice;
    }

    private function flagInvoiceAsPaidAndAssertState(Invoice $invoice): Invoice
    {
        // Act: Flag as Paid
        $invoice->flagAsPaid();

        // Assert: Aggregate should now have 4 events
        $this->assertAggregateState(
            invoice: $invoice,
            expectedDomainEventCount: 1,
            expectedAggregateVersion: 4,
            expectedLineItemCount: 2
        );

        return $invoice;
    }

    private function persistAndRestoreAggregateThirdTime(Invoice $invoice): Invoice
    {
        // Act: Persist and restore the aggregate
        $this->repository->persist($invoice);
        $invoice = $this->repository->restore($this->aggregateId, Invoice::class);

        $this->assertAggregateState(
            invoice: $invoice,
            expectedDomainEventCount: 0,
            expectedAggregateVersion: 4,
            expectedLineItemCount: 2
        );

        return $invoice;
    }

    public function testEventSourcedRepositoryIntegration(): void
    {
        $invoice = $this->createInvoice();
        $invoice = $this->persistAndRestoreTheAggregateFirstTime($invoice);
        $invoice = $this->addLineItemAndAssertAggregateState($invoice);
        $invoice = $this->persistAndRestoreAggregateSecondTime($invoice);
        $invoice = $this->flagInvoiceAsPaidAndAssertState($invoice);
        $this->persistAndRestoreAggregateThirdTime($invoice);
    }
}
