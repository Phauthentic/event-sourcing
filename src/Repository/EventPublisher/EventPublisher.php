<?php

declare(strict_types=1);

namespace Phauthentic\EventSourcing\Repository\EventPublisher;

use Iterator;
use RuntimeException;

/**
 *
 */
class EventPublisher implements EventPublisherInterface
{
    /**
     * @param array<int, EventPublisherMiddlewareInterface> $middlewares
     */
    public function __construct(
        protected array $middlewares = [],
    ) {
        $this->assertNonEmptyMiddlewareStack();
    }

    protected function assertNonEmptyMiddlewareStack(): void
    {
        if (empty($this->middlewares)) {
            throw new RuntimeException('No middleware registered!');
        }
    }

    public function emitEvent(object $event): void
    {
        foreach ($this->middlewares as $middleware) {
            if (!is_callable($middleware)) {
                throw new RuntimeException('Non-callable subscriber!');
            }

            $middleware($event);
        }
    }

    public function emitEvents(Iterator|array|\Generator $events): void
    {
        foreach ($events as $event) {
            $this->emitEvent($event);
        }
    }
}
