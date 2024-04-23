<?php

declare(strict_types=1);

namespace Phauthentic\EventSourcing\Repository;

use Phauthentic\EventSourcing\Repository\EventPublisher\EventPublisherInterface;
use Phauthentic\EventStore\EventStoreInterface;
use Psr\Container\ContainerInterface;

/**
 *
 */
class EventSourcedRepositoryFactory
{
    public function __construct(
        protected ContainerInterface $container,
    ) {
    }

    public function createRepositoryFromClass(string $class): object
    {
        return new $class(
            $this->container->get(EventStoreInterface::class),
            null,
            null,
            $this->container->get(EventPublisherInterface::class)
        );
    }
}
