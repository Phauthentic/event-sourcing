<?php

declare(strict_types=1);

namespace Example\Domain\Invoice;

/**
 *
 */
class LineItem
{
    public function __construct(
        public readonly string $sku,
        public readonly string $name,
        public float $price
    ) {
    }

    /**
     * Create a new LineItem instance
     *
     * @param string $sku
     * @param string $name
     * @param float $price
     * @return LineItem
     */
    public static function create(
        string $sku,
        string $name,
        float $price
    ): self {
        return new self(
            sku: $sku,
            name: $name,
            price: $price
        );
    }
}
