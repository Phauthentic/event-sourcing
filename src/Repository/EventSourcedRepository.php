<?php

declare(strict_types=1);

namespace Phauthentic\EventSourcing\Repository;

use DateTimeImmutable;
use Phauthentic\EventSourcing\Repository\AggregateExtractor\AggregateExtractorInterface;
use Phauthentic\EventSourcing\Repository\AggregateFactory\AggregateFactoryInterface;
use Phauthentic\EventSourcing\Repository\EventPublisher\EventPublisherInterface;
use Phauthentic\EventSourcing\Repository\SnapshotStrategy\SnapshotStrategyInterface;
use Phauthentic\EventStore\EventFactoryInterface;
use Phauthentic\EventStore\EventInterface;
use Phauthentic\EventStore\EventStoreInterface;
use Phauthentic\EventStore\ReplyFromPositionQuery;
use Phauthentic\SnapshotStore\SnapshotFactoryInterface;
use Phauthentic\SnapshotStore\SnapshotInterface;
use Phauthentic\SnapshotStore\Store\SnapshotStoreInterface;

/**
 * The event sourced repository can be used directly or extended.
 *
 * It is a composition of multiple clearly separated components that it connects to build a repository that can be
 * used with event sourced aggregates.
 *
 * - An event store that stores the events extracted from the aggregate object.
 * - An aggregate extractor that extracts all relevant data from the aggregate object.
 * - A snapshot store that can take serialized snapshots of aggregate objects.
 * - An event publisher that is a wrapper to connect the aggregate with whatever
 *   event system you are using. Just wrap with a class implementing the EventPublisherInterface.
 */
readonly class EventSourcedRepository implements EventSourcedRepositoryInterface
{
    /**
     * EventSourcedRepository constructor.
     *
     * @param EventStoreInterface $eventStore
     * @param AggregateExtractorInterface $aggregateExtractor
     * @param AggregateFactoryInterface $aggregateFactory
     * @param EventFactoryInterface $eventFactory
     * @param SnapshotStoreInterface $snapshotStore
     * @param SnapshotFactoryInterface $snapshotFactory
     * @param EventPublisherInterface|null $eventPublisher
     * @param array<int, SnapshotStrategyInterface> $snapshotStrategies
     */
    public function __construct(
        protected EventStoreInterface $eventStore,
        protected AggregateExtractorInterface $aggregateExtractor,
        protected AggregateFactoryInterface $aggregateFactory,
        protected EventFactoryInterface $eventFactory,
        protected SnapshotStoreInterface $snapshotStore,
        protected SnapshotFactoryInterface $snapshotFactory,
        protected ?EventPublisherInterface $eventPublisher = null,
        protected array $snapshotStrategies = []
    ) {
    }

    /**
     * @inheritDoc
     */
    public function persist(object $aggregate): void
    {
        $aggregateData = $this->aggregateExtractor->extract($aggregate);

        $this->storeEvents($aggregateData);

        if ($this->hasSnapshotStore()) {
            $this->applySnapshotStrategies($aggregate, $aggregateData);
        }
    }

    protected function hasSnapshotStore(): bool
    {
        return $this->snapshotStore !== null;
    }

    protected function storeEvents(AggregateDataInterface $aggregateData): void
    {
        $version = $aggregateData->getAggregateVersion() - count($aggregateData->getDomainEvents());

        foreach ($aggregateData->getDomainEvents() as $event) {
            $version++;
            $storeEvent = $this->eventFactory->createEventFromArray([
                EventInterface::STREAM => (string)$aggregateData->getStream(),
                EventInterface::AGGREGATE_ID => $aggregateData->getAggregateId(),
                EventInterface::VERSION => $version,
                EventInterface::EVENT => get_class($event),
                EventInterface::PAYLOAD => $event,
                EventInterface::CREATED_AT => (new DateTimeImmutable())->format(EventInterface::CREATED_AT_FORMAT)
            ]);

            $this->eventStore->storeEvent($storeEvent);
            $this->eventPublisher?->emitEvent($event);
        }
    }

    /**
     * Applies the snapshot strategies if applicable.
     *
     * The first applicable strategy will be used to take a snapshot.
     */
    protected function applySnapshotStrategies(object $aggregate, AggregateDataInterface $aggregateData): void
    {
        foreach ($this->snapshotStrategies as $snapshotStrategy) {
            if ($snapshotStrategy->isApplicable($aggregateData)) {
                $this->takeSnapshot($aggregate);
                break;
            }
        }
    }

    public function restore(string $aggregateId, string $aggregateType): object
    {
        $aggregate = $aggregateType;
        $position = 0;

        if ($this->hasSnapshotStore()) {
            $snapshot = $this->getSnapshot($aggregateId);

            if ($snapshot) {
                $aggregate = $snapshot->getAggregateRoot();
                $position = $snapshot->getLastVersion();
            }
        }

        $events = $this->eventStore->replyFromPosition(new ReplyFromPositionQuery(
            aggregateId: $aggregateId,
            position: $position + 1
        ));

        return $this->aggregateFactory->reconstituteFromEvents($aggregate, $events);
    }

    protected function takeSnapshot(object $aggregate): void
    {
        $aggregateData = $this->aggregateExtractor->extract($aggregate);

        $snapshot = $this->snapshotFactory->fromArray([
            SnapshotInterface::AGGREGATE_TYPE => $aggregateData->getAggregateType(),
            SnapshotInterface::AGGREGATE_ID => $aggregateData->getAggregateId(),
            SnapshotInterface::AGGREGATE_ROOT => $aggregate,
            SnapshotInterface::AGGREGATE_VERSION => $aggregateData->getAggregateVersion(),
            SnapshotInterface::AGGREGATE_CREATED_AT => new DateTimeImmutable(),
        ]);

        $this->snapshotStore->store($snapshot);
    }

    /**
     * @param string $aggregateId
     * @return null|SnapshotInterface
     */
    protected function getSnapshot(string $aggregateId): ?SnapshotInterface
    {
        return $this->snapshotStore->get($aggregateId);
    }
}
