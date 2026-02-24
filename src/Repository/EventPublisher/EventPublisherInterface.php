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

namespace Phauthentic\EventSourcing\Repository\EventPublisher;

use Generator;
use Iterator;

/**
 *
 */
interface EventPublisherInterface
{
    /**
     * @param object $event
     * @return void
     */
    public function emitEvent(object $event): void;

    /**
     * If the underlying implementation is able to batch process events
     * use this method.
     *
     * @param array<int, object>|\Generator|\Iterator $events
     * @return void
     */
    public function emitEvents(array|Generator|Iterator $events): void;
}
