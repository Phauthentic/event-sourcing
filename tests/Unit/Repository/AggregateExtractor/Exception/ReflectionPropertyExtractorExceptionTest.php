<?php

declare(strict_types=1);

namespace Phauthentic\EventSourcing\Test\Unit\Repository\AggregateExtractor\Exception;

use Phauthentic\EventSourcing\Repository\AggregateExtractor\Exception\ReflectionPropertyExtractorException;
use PHPUnit\Framework\TestCase;

/**
 *
 */
class ReflectionPropertyExtractorExceptionTest extends TestCase
{
    public function testClassHasMissingProperty(): void
    {
        $exception = ReflectionPropertyExtractorException::classHasMissingProperty('TestClass', 'testProperty');

        $this->assertInstanceOf(ReflectionPropertyExtractorException::class, $exception);
        $this->assertEquals('Aggregate class `TestClass` is missing the property `testProperty`', $exception->getMessage());
    }
}
