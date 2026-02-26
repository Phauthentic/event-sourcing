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
 * Interface for projectors that support resetting their read models.
 *
 * This is useful for rebuilding projections from scratch when the event store
 * has been replayed or when projection data becomes corrupted.
 */
interface ResettableProjectorInterface extends ProjectorInterface
{
    /**
     * Resets the projection read model to its initial state.
     *
     * This typically involves clearing all projection data so it can be
     * rebuilt from events.
     *
     * @return void
     */
    public function reset(): void;
}