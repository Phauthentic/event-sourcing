<?php

declare(strict_types=1);

namespace Phauthentic\EventSourcing\Test\Benchmark;

use AllowDynamicProperties;
use Example\Domain\Invoice\Address;
use Example\Domain\Invoice\Invoice;
use Example\Domain\Invoice\InvoiceId;
use Example\Domain\Invoice\LineItem;
use PDO;
use Phauthentic\EventSourcing\Repository\AggregateExtractor\AttributeBasedExtractor;
use Phauthentic\EventSourcing\Repository\AggregateFactory\ReflectionFactory;
use Phauthentic\EventSourcing\Repository\EventSourcedRepository;
use Phauthentic\EventSourcing\Repository\SnapshotStrategy\EveryVersionStrategy;
use Phauthentic\EventStore\EventFactory;
use Phauthentic\EventStore\EventStoreInterface;
use Phauthentic\EventStore\InMemoryEventStore;
use Phauthentic\EventStore\PdoEventStore;
use Phauthentic\EventStore\Serializer\SerializeSerializer;
use Phauthentic\SnapshotStore\SnapshotFactory;
use Phauthentic\SnapshotStore\Store\InMemorySnapshotStore;
use Ramsey\Uuid\Uuid;
use RuntimeException;

#[AllowDynamicProperties]
class EventStoryBenchmark
{
    private InMemorySnapshotStore $snapshotStore;
    private AttributeBasedExtractor $extractor;
    private ReflectionFactory $aggregateFactory;
    private EventFactory $eventFactory;
    private SnapshotFactory $snapshotFactory;
    private string $aggregateId;

    public function __construct()
    {
        $this->snapshotStore = new InMemorySnapshotStore();
        $this->extractor = new AttributeBasedExtractor();
        $this->aggregateFactory = new ReflectionFactory();
        $this->eventFactory = new EventFactory();
        $this->snapshotFactory = new SnapshotFactory();

        $this->aggregateId = Uuid::uuid4()->toString();
    }

    protected function createPdoEventStore(): PdoEventStore
    {
        $host = getenv('DB_HOST') ?: 'event-sourcing-mysql';
        $dbname = getenv('DB_DATABASE') ?: 'test';
        $user = getenv('DB_USER') ?: 'root';
        $password = getenv('DB_PASSWORD') ?: 'changeme';

        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
        $pdo->query("DROP TABLE IF EXISTS `event_store`;");

        $query = file_get_contents('./vendor/phauthentic/event-store/resources/event_store.sql');
        if ($query === false) {
            throw new RuntimeException('Could not read snapshot_store.sql');
        }

        $pdo->query('use test');
        $pdo->query($query);

        return new PDOEventStore(
            pdo: $pdo,
            serializer: new SerializeSerializer(),
            eventFactory: new EventFactory(),
        );
    }

    protected function createInMemoryEventStory(): InMemoryEventStore
    {
        return new InMemoryEventStore();
    }

    protected function createRepository(EventStoreInterface $eventStore): EventSourcedRepository
    {
        return new EventSourcedRepository(
            eventStore: $eventStore,
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

    public function benchInMemoryEventStore(): void
    {
        $repository = $this->createRepository($this->createInMemoryEventStory());
        $this->createInvoice($repository);
    }

    public function benchPdoEventStore(): void
    {
        $repository = $this->createRepository($this->createPdoEventStore());
        $this->createInvoice($repository);
    }

    protected function createInvoice(EventSourcedRepository $repository)
    {
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

        $repository->persist($invoice);

        for ($i = 0; $i < 1000; $i++) {
            $invoice->addLineItem(
                LineItem::create(
                    sku: 'beer-' . $i,
                    name: 'Beer',
                    price: (float)random_int(1, 1000)
                )
            );

            if ($i % 100 === 0) {
                $repository->persist($invoice);
                $invoice = $repository->restore($this->aggregateId, Invoice::class);
            }
        }
    }
}
