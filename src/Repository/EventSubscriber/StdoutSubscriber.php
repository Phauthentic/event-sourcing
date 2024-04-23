<?php

declare(strict_types=1);

namespace Phauthentic\EventSourcing\Repository\EventSubscriber;

use RuntimeException;

/**
 * Can be used a simple, convenient debugging helper. Should not be used in production.
 */
class StdoutSubscriber
{
    /**
     * @var resource
     */
    private $outputStream;

    /**
     * Constructor
     */
    public function __construct()
    {
        $resource = fopen('php://stdout', 'wb');
        if (!$resource) {
            throw new RuntimeException('Could not open php://stdout');
        }

        $this->outputStream = $resource;
    }

    public function __invoke(object $event): void
    {
        fwrite($this->outputStream, "Event emitted: " . get_class($event) . PHP_EOL);
    }
}
