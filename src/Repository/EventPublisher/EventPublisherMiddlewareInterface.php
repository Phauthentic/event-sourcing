<?php

declare(strict_types=1);

namespace Phauthentic\EventSourcing\Repository\EventPublisher;

/**
 *
 */
interface EventPublisherMiddlewareInterface
{
    public function handle(object $event): void;
}
