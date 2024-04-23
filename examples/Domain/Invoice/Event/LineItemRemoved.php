<?php

declare(strict_types=1);

namespace Example\Domain\Invoice\Event;

/**
 *
 */
class LineItemRemoved
{
	public function __construct(
		public readonly string $sku,
		public readonly string $name,
		public float $price
	) {}
}
