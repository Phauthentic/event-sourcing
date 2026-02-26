<?php

declare(strict_types=1);

namespace Phauthentic\EventSourcing\Test\Unit\Repository\AggregateExtractor\Exception;

use Phauthentic\EventSourcing\Repository\AggregateExtractor\Exception\ExtractorException;
use PHPUnit\Framework\TestCase;

/**
 *
 */
class ExtractorExceptionTest extends TestCase
{
    public function testNotEmptyValue(): void
    {
        $exception = ExtractorException::notEmptyValue('testProperty');

        $this->assertInstanceOf(ExtractorException::class, $exception);
        $this->assertEquals('The value of `testProperty` can not be empty.', $exception->getMessage());
    }
}
