<?php

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
    public static function eventDoesNotMatchAggregateWith(object $event, object $aggregate): EventMismatchException
    {
        return new self(sprintf(
            self::EVENT_DOES_NOT_MATCH_AGGREGATE,
            get_class($event),
            get_class($aggregate)
        ));
    }
}
