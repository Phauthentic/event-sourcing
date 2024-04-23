<?php

declare(strict_types=1);

namespace Phauthentic\EventSourcing\Repository\AggregateExtractor\Exception;

use Exception;

/**
 *
 */
class ReflectionPropertyExtractorException extends ExtractorException
{
    /**
     * @param string $className
     * @param $propertyName
     * @return \Phauthentic\EventSourcing\Repository\AggregateExtractor\Exception\ReflectionPropertyExtractorException
     */
    public static function classHasMissingProperty(
        string $className,
        string $propertyName
    ): ReflectionPropertyExtractorException {
        return new self(sprintf('Aggregate class `%s` is missing the property `%s`', $className, $propertyName));
    }
}
