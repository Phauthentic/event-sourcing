<?php

declare(strict_types=1);

namespace Phauthentic\EventSourcing\Aggregate;

/**
 *
 */
interface TypeProvidingAggregateInterface
{
    public function getAggregateType(): string;
}
