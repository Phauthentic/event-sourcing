<?php

declare(strict_types=1);

namespace Example\Domain\Invoice\Event;

use Example\Domain\Invoice\Address;

/**
 *
 */
class InvoiceCreated
{
    public function __construct(
        public readonly string $invoiceId,
        public readonly Address $address,
    ) {
    }
}
