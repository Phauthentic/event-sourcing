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
