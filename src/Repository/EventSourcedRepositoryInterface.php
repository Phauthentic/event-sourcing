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
     */
    public function persist(object $aggregate): void;

    /**
     * @param string $aggregateId Restores an aggregate by its aggregateId
     * @param string $aggregateType The aggregate type
     *
     * @return object
     */
    public function restore(string $aggregateId, string $aggregateType): object;
}
