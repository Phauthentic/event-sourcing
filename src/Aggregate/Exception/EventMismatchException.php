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
class EventMismatchException extends AggregateException
{
    protected const EVENT_DOES_NOT_MATCH_AGGREGATE = 'The event `%s` does not match the aggregate `%s`';

    /**
     * @param object $event
     * @param object $aggregate
     * @return EventMismatchException
     */
    public static function eventDoesNotMatchAggregateWith(object $event, object $aggregate): self
    {
        return new self(sprintf(
            self::EVENT_DOES_NOT_MATCH_AGGREGATE,
            get_class($event),
            get_class($aggregate)
        ));
    }
}
