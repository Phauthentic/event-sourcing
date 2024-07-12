<?php

declare(strict_types=1);

namespace Example\Domain\Invoice\Event;

/**
 *
 */
class InvoicePaid
{
    private function __construct(
        private string $invoiceId,
    )
    {
    }

    public static function create(
        string $invoiceId
    )
    {
        return new self($invoiceId);
    }

    public function getInvoiceId()
    {
        return $this->invoiceId;
    }
}
