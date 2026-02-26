<?php

/**
 * Copyright (c) Florian Krämer (https://florian-kraemer.net)
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE file
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Florian Krämer (https://florian-kraemer.net)
 * @author    Florian Krämer
 * @link      https://github.com/Phauthentic
 * @license   https://opensource.org/licenses/MIT MIT License
 */

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
