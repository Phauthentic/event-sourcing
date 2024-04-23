<?php

declare(strict_types=1);

namespace Phauthentic\EventSourcing\Repository\EventPublisher;

use Symfony\Component\Messenger\MessageBus;

/**
 *
 */
class SymfonyMessageBusConnectorMiddleware implements EventPublisherMiddlewareInterface
{
    public function __construct(
        protected MessageBus $eventBus
    ) {
    }

    public function handle(object $event): void
    {
        $this->eventBus->dispatch($event);
    }
}
