<?php

declare(strict_types=1);

namespace Phauthentic\EventSourcing\Test\Unit\Repository;

use Phauthentic\EventSourcing\Repository\EventSourcedRepositoryFactory;
use Phauthentic\EventSourcing\Repository\EventPublisher\EventPublisherInterface;
use Phauthentic\EventStore\EventStoreInterface;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 *
 */
class EventSourcedRepositoryFactoryTest extends TestCase
{
    public function testConstruct(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $factory = new EventSourcedRepositoryFactory($container);

        $this->assertInstanceOf(EventSourcedRepositoryFactory::class, $factory);
    }

    public function testCreateRepositoryFromClass(): void
    {
        $eventStore = $this->createMock(EventStoreInterface::class);
        $eventPublisher = $this->createMock(EventPublisherInterface::class);

        $container = $this->createMock(ContainerInterface::class);
        $container->method('get')
            ->willReturnCallback(function ($service) use ($eventStore, $eventPublisher) {
                return match ($service) {
                    EventStoreInterface::class => $eventStore,
                    EventPublisherInterface::class => $eventPublisher,
                    default => null,
                };
            });

        $factory = new EventSourcedRepositoryFactory($container);
        $repository = $factory->createRepositoryFromClass('stdClass');

        $this->assertInstanceOf('stdClass', $repository);
    }
}
