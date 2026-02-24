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

use Psr\Log\LoggerInterface;

/**
 *
 */
class EventLoggerMiddleware implements EventPublisherMiddlewareInterface
{
    /**
     * Constructor
     *
     * @param LoggerInterface $logger
     */
    public function __construct(
        protected LoggerInterface $logger,
    ) {
    }

    public function handle(object $event): void
    {
        $this->logger->info(sprintf('Event %s emitted.', get_class($event)));
    }

    public function isInterrupting(): bool
    {
        return false;
    }
}
