<?php

declare(strict_types=1);

namespace Phauthentic\EventSourcing\Repository\AggregateExtractor\Exception;

use Phauthentic\EventSourcing\EventSourcingException;

/**
 *
 */
class ReflectionPropertyExtractorException extends EventSourcingException
{
    /**
     * @param string $className
     * @param string $propertyName
     * @return ReflectionPropertyExtractorException
     */
    public static function classHasMissingProperty(
        string $className,
        string $propertyName
    ): self {
        return new self(sprintf('Aggregate class `%s` is missing the property `%s`', $className, $propertyName));
    }
}
