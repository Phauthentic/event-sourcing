<?php

declare(strict_types=1);

namespace Phauthentic\EventSourcing\DomainEvent;

/**
 *
 */
interface TypeProvidingDomainEventInterface
{
    /**
     * Provides the name an event.
     *
     * @return string
     */
    public function getEventType(): string;
}
