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

use Symfony\Component\Messenger\MessageBus;

/**
 *
 */
class SymfonyMessageBusConnectorMiddleware implements EventPublisherMiddlewareInterface
{
    public function __construct(
        protected MessageBus $eventBus
    ) {
    }

    public function handle(object $event): void
    {
        $this->eventBus->dispatch($event);
    }

    public function isInterrupting(): bool
    {
        return false;
    }
}
