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

/**
 * A repository that is responsible for persisting and restoring event sourced aggregates.
 */
interface EventSourcedRepositoryInterface
{
    /**
     * Persists an aggregates events in the event store.
     *
     * @param object $aggregate The aggregate
     */
    public function persist(object $aggregate): void;

    /**
     * Restores an aggregates state.
     *
     * @param string $aggregateId Restores an aggregate by its aggregateId
     * @param string $aggregateType The aggregate type
     *
     * @return object
     */
    public function restore(string $aggregateId, string $aggregateType): object;
}
