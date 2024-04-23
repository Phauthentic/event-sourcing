<?php

declare(strict_types=1);

namespace Phauthentic\EventSourcing\DomainEvent\Attribute;

use Attribute;

/**
 *
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class CorrelationId
{
    public function __construct(
        public readonly ?string $value = null
    ) {
    }
}
