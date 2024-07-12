<?php

declare(strict_types=1);

namespace Example\Domain\Invoice;

use RuntimeException;

class InvoiceException extends RuntimeException
{
    public static function mustHaveAtLeastOneLineItem(): self
    {
        return new self('Your invoice must have at least one line item!');
    }

    public static function invoiceIsAlreadyPaid(): self
    {
        return new self('The invoice is already paid!');
    }
}

