<?php

declare(strict_types=1);

namespace Phauthentic\EventSourcing\DomainEvent\Attribute;

use Attribute;
use DateTimeImmutable;

/**
 *
 */
#[Attribute(Attribute::TARGET_CLASS)]
class EventCreatedAt
{
    protected DateTimeImmutable $value;

    public function __construct()
    {
        $this->value = new DateTimeImmutable();
    }
}
