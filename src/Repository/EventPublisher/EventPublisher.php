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
     * @var array<int, EventPublisherMiddlewareInterface> $middlewares
     */
    protected array $middlewares = [];

    /**
     * @param array<int, EventPublisherMiddlewareInterface> $middlewares
     */
    public function __construct(
        array $middlewares = []
    ) {
        foreach ($middlewares as $middleware) {
            $this->addMiddleware($middleware);
        }

        $this->assertNonEmptyMiddlewareStack();
    }

    public function addMiddleware(EventPublisherMiddlewareInterface $middleware): void
    {
        $this->middlewares[] = $middleware;
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
            $middleware->handle($event);
            if ($middleware->isInterrupting()) {
                break;
            }
        }
    }

    public function emitEvents(Iterator|array|\Generator $events): void
    {
        foreach ($events as $event) {
            $this->emitEvent($event);
        }
    }
}
