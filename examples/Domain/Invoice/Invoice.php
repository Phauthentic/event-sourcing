<?php

declare(strict_types=1);

namespace Example\Domain\Invoice;

use Example\Domain\Invoice\Event\InvoiceCreated;
use Example\Domain\Invoice\Event\InvoicePaid;
use Example\Domain\Invoice\Event\LineItemAdded;
use Example\Domain\Invoice\Event\LineItemRemoved;
use Phauthentic\EventSourcing\Aggregate\AbstractEventSourcedAggregate;
use Phauthentic\EventSourcing\Aggregate\Attribute\AggregateIdentifier;
use Phauthentic\EventSourcing\Aggregate\Attribute\AggregateVersion;
use Phauthentic\EventSourcing\Aggregate\Attribute\DomainEvents;

/**
 *
 */
final class Invoice extends AbstractEventSourcedAggregate
{
    #[AggregateIdentifier]
    protected string $aggregateId = '';

    #[AggregateVersion]
    protected int $aggregateVersion = 0;

    #[DomainEvents]
    protected array $aggregateEvents = [];

    private array $lineItems = [];

    private bool $paid = false;

    private Address $address;

    private float $gross = 0.00;

    private function __construct()
    {
    }

    public function getInvoiceId(): InvoiceId
    {
        return InvoiceId::fromString($this->aggregateId);
    }

    /**
     * @param array<int, LineItem> $lineItems
     * @return void
     */
    protected function assertAtLeastOneLineItem(array $lineItems): void
    {
        assert(count($lineItems) > 0, 'Your invoice must have at least one line item!');
    }

    public static function create(
        InvoiceId $invoiceId,
        Address $address,
        array $lineItems
    ): Invoice {
        $that = new self();
        $that->aggregateId = (string)$invoiceId;
        $that->address = $address;

        $that->assertAtLeastOneLineItem($lineItems);

        $that->recordThat(new InvoiceCreated(
            invoiceId: (string)$invoiceId,
            address: $address
        ));

        foreach ($lineItems as $lineItem) {
            $that->addLineItem($lineItem);
        }

        return $that;
    }

    protected function assertInvoiceWasNotPaid()
    {
        assert(!$this->paid, 'The invoice was paid');
    }

    public function addLineItem(LineItem $lineItem)
    {
        $this->assertInvoiceWasNotPaid();

        $this->lineItems[] = $lineItem;
        $this->gross += $lineItem->price;

        $this->recordThat(new LineItemAdded(
            sku: $lineItem->sku,
            name: $lineItem->name,
            price: $lineItem->price
        ));
    }

    public function removeLineItem(string $sku): void
    {
        foreach ($this->lineItems as $key => $lineItem) {
            if ($lineItem->sku === $sku) {
                unset($this->lineItems[$key]);

                $this->recordThat(new LineItemRemoved(
                    sku: $lineItem->sku,
                    name: $lineItem->name,
                    price: $lineItem->price
                ));

                return;
            }
        }
    }

    public function flagAsPaid()
    {
        $this->assertInvoiceWasNotPaid();

        $this->paid = true;

        $this->recordThat(new InvoicePaid());
    }

    public function lineItemCount(): int
    {
        return count($this->lineItems);
    }

    public function getDomainEventCount(): int
    {
        return count($this->aggregateEvents);
    }

    public function getAggregateVersion(): int
    {
        return $this->aggregateVersion;
    }

    public function getGross(): float
    {
        return $this->gross;
    }

    public function getAddress(): Address
    {
        return $this->address;
    }

    protected function whenLineItemAdded(LineItemAdded $event)
    {
        $this->lineItems[$event->sku] = new LineItem(
            sku: $event->sku,
            name: $event->name,
            price: $event->price
        );
    }

    protected function whenInvoiceCreated(InvoiceCreated $event)
    {
        $this->aggregateId = $event->invoiceId;
        $this->address = $event->address;
    }

    protected function whenInvoicePaid(InvoicePaid $event)
    {
        $this->paid = true;
    }
}
