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

namespace Phauthentic\EventSourcing\Projection;

/**
 * Interface for event projectors that build read models from domain events.
 *
 * Projectors transform domain events into optimized read models that can be queried
 * efficiently by the application layer.
 */
interface ProjectorInterface
{
    /**
     * Determines if this projector can handle the given event.
     *
     * @param object $event Domain event
     * @return bool True if this projector supports the event
     */
    public function supports(object $event): bool;

    /**
     * Projects the event into the read model.
     *
     * @param object $event Domain event to project
     * @return void
     */
    public function project(object $event): void;
}
