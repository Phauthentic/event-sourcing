<?php

declare(strict_types=1);

namespace Example\Domain\Invoice\Event;

/**
 *
 */
class LineItemAdded
{
    public function __construct(
        public readonly string $sku,
        public readonly string $name,
        public float $price
    ) {
    }
}
