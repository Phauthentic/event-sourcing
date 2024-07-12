<?php

declare(strict_types=1);

namespace Phauthentic\EventSourcing\Repository\EventPublisher;

use Psr\Log\LoggerInterface;

/**
 *
 */
class EventLoggerMiddleware implements EventPublisherMiddlewareInterface
{
    /**
     * Constructor
     *
     * @param LoggerInterface $logger
     */
    public function __construct(
        protected LoggerInterface $logger,
    ) {
    }

    public function handle(object $event): void
    {
        $this->logger->info(sprintf('Event %s emitted.', get_class($event)));
    }

    public function isInterrupting(): bool
    {
        return false;
    }
}
