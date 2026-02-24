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

namespace Phauthentic\EventSourcing\Aggregate;

/**
 *
 */
interface EventSourcedAggregateInterface
{
    /**
     * Returns the ID of the aggregate as string
     */
    public function getAggregateId(): string;

    /**
     * Get the version of the aggregate
     */
    public function getAggregateVersion(): int;

    /**
     * - Returns a list of events
     * - Resets the events to an empty list
     *
     * @return array<int, object>
     */
    public function consumeAggregateEvents(): array;
}
