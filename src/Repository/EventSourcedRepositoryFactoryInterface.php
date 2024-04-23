<?php

declare(strict_types=1);

namespace Phauthentic\EventSourcing\Repository;

use App\Infrastructure\EventBus\EventBusInterface;
use App\Infrastructure\EventStore\EventStoreInterface;
use Psr\Container\ContainerInterface;

/**
 *
 */
interface EventSourcedRepositoryFactoryInterface
{
    public function createRepositoryFromClassString(string $class): EventSourcedRepository;
}
