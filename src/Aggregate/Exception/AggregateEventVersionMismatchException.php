<?php

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
    public static function fromVersions(int $aggregateVersion, int $eventVersion)
    {
        return new self(sprintf(
            self::MESSAGE_STRING,
            $aggregateVersion,
            $eventVersion
        ));
    }
}
