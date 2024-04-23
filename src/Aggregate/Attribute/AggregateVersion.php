<?php

declare(strict_types=1);

namespace Phauthentic\EventSourcing\Aggregate\Attribute;

use Attribute;

/**
 *
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class AggregateVersion
{
    public function __construct(
        public readonly ?int $value = null
    ) {
    }
}
