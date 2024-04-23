<?php

declare(strict_types=1);

namespace Example\Domain\Invoice;

/**
 *
 */
class InvoiceId
{
    private string $id = '';

    private function __construct()
    {
    }

    public static function fromString(string $id)
    {
        $that = new self();
        $that->id = $id;

        return $that;
    }

    public function __toString()
    {
        return $this->id;
    }
}
