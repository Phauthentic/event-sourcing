<?php

declare(strict_types=1);

namespace Phauthentic\EventSourcing\Aggregate;

/**
 *
 */
abstract class AbstractEventSourcedAggregate
{
    use EventSourcedAggregateTrait;
}
