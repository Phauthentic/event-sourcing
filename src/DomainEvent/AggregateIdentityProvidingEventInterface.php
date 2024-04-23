<?php

declare(strict_types=1);

namespace Phauthentic\EventSourcing\DomainEvent;

/**
 *
 */
interface AggregateIdentityProvidingEventInterface
{
    public function getAggregateId(): string;

    public function getAggregateVersion(): int;
}
