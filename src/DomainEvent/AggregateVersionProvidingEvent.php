<?php

declare(strict_types=1);

namespace Phauthentic\EventSourcing\DomainEvent;

/**
 *
 */
interface AggregateVersionProvidingEvent
{
    public function getAggregateVersion(): int;
}
