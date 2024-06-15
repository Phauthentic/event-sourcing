<?php

declare(strict_types=1);

namespace Phauthentic\EventSourcing\Repository\AggregateExtractor\Exception;

use Phauthentic\EventSourcing\EventSourcingException;

/**
 *
 */
class ExtractorException extends EventSourcingException
{
    /**
     * @param string $name
     * @return \Phauthentic\EventSourcing\Repository\AggregateExtractor\Exception\ExtractorException
     */
    public static function notEmptyValue(string $name)
    {
        return new self(sprintf('The value of `%s` can not be empty.', $name));
    }
}
