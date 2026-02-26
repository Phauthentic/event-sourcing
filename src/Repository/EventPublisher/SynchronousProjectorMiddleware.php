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

use Phauthentic\EventSourcing\Projection\ProjectorInterface;

/**
 * Middleware that synchronously executes projectors for domain events.
 *
 * This middleware allows projections to be built synchronously as part of the
 * event publishing pipeline. It supports configurable exception handling:
 * - Fail-fast mode: stops processing on first projector error
 * - Non-interrupting mode: continues processing other projectors even if one fails
 */
class SynchronousProjectorMiddleware implements EventPublisherMiddlewareInterface
{
    /**
     * @var array<int, ProjectorInterface>
     */
    private array $projectors = [];

    /**
     * @param array<int, ProjectorInterface> $projectors
     * @param bool $failFast Whether to stop on first projector error (true) or continue (false)
     */
    public function __construct(
        array $projectors = [],
        private bool $failFast = true
    ) {
        foreach ($projectors as $projector) {
            $this->addProjector($projector);
        }
    }

    /**
     * Adds a projector to the middleware.
     */
    public function addProjector(ProjectorInterface $projector): void
    {
        $this->projectors[] = $projector;
    }

    /**
     * Handles a domain event by projecting it through all registered projectors.
     *
     * @param object $event Domain event to project
     * @return void
     */
    public function handle(object $event): void
    {
        $errors = [];

        foreach ($this->projectors as $projector) {
            if (!$projector->supports($event)) {
                continue;
            }

            try {
                $projector->project($event);
            } catch (\Throwable $e) {
                if ($this->failFast) {
                    throw $e;
                }
                $errors[] = $e;
            }
        }

        // If not failing fast and we have errors, we could log them here
        // For now, we'll just silently collect them since the interface
        // doesn't allow returning error information
    }

    /**
     * Whether this middleware should interrupt the middleware chain.
     *
     * @return bool Always returns false to allow other middleware to process
     */
    public function isInterrupting(): bool
    {
        return false;
    }
}