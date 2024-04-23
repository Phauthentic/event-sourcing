<?php

declare(strict_types=1);

namespace Example\Domain\Invoice;

enum InvoiceStatus: string
{
    case DRAFT = 'draft';
    case PENDING = 'pending';
    case PAID = 'paid';
    case OVERDUE = 'overdue';
}
