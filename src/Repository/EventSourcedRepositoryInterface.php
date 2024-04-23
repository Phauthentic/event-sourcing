<?php

declare(strict_types=1);

namespace Phauthentic\EventSourcing\Repository;

/**
 *
 */
interface EventSourcedRepositoryInterface
{
    /**
     * @param object $aggregate The aggregate
     * @param bool $takeSnapshot If a snapshot should be taken or not
     */
    public function persist(object $aggregate, bool $takeSnapshot = false): void;

    /**
     * @param string $aggregateId Restores an aggregate by its aggregateId
     * @param string $aggregateType The aggregate type
     *
     * @return object
     */
    public function restore(string $aggregateId, string $aggregateType): object;
}
