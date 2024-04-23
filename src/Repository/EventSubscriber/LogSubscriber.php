<?php

declare(strict_types=1);

namespace Phauthentic\EventSourcing\Repository\EventSubscriber;

use Psr\Log\LoggerInterface;

class LogSubscriber
{
    /**
     * Constructor
     *
     * @param LoggerInterface $logger
     */
    public function __construct(
        protected LoggerInterface $logger
    ) {
    }

    public function __invoke(object $event): void
    {
        $this->logger->info('Event emitted: ' . get_class($event) . PHP_EOL);
    }
}
