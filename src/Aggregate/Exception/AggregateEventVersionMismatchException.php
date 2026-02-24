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

namespace Phauthentic\EventSourcing\Aggregate\Exception;

/**
 *
 */
class AggregateEventVersionMismatchException extends AggregateException
{
    protected const MESSAGE_STRING = 'Event version does not match the sequence: '
    . 'Aggregate is on %d, event wants to apply %d';

    /**
     * @param int $aggregateVersion
     * @param int $eventVersion
     * @return self
     */
    public static function fromVersions(int $aggregateVersion, int $eventVersion): self
    {
        return new self(sprintf(
            self::MESSAGE_STRING,
            $aggregateVersion,
            $eventVersion
        ));
    }
}
