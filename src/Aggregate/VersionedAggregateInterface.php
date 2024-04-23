<?php

declare(strict_types=1);

namespace Phauthentic\EventSourcing\Aggregate;

/**
 * If an aggregate should expose its internal version use this interface.
 */
interface VersionedAggregateInterface
{
    /**
     * Get the version of the aggregate
     */
    public function getAggregateVersion(): int;
}
