<?php

/**
 * Copyright (c) Florian Krämer (https://florian-kraemer.net)
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE file
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Florian Krämer (https://florian-kraemer.net)
 * @author    Florian Krämer
 * @link      https://github.com/Phauthentic
 * @license   https://opensource.org/licenses/MIT MIT License
 */

declare(strict_types=1);

namespace Phauthentic\EventSourcing\DomainEvent;

/**
 *
 */
abstract class AbstractDomainEvent implements
    AggregateIdentityProvidingEventInterface,
    AggregateVersionProvidingEvent,
    TypeProvidingDomainEventInterface
{
    protected string $aggregateId;

    protected int $aggregateVersion;

    protected string $domainEventType;

    public function getAggregateId(): string
    {
        return $this->aggregateId;
    }

    public function getAggregateVersion(): int
    {
        return $this->aggregateVersion;
    }

    public function getEventType(): string
    {
        return $this->domainEventType;
    }
}
