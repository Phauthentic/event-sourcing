<?php

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
