<?php

declare(strict_types=1);

namespace Phauthentic\EventSourcing\Test\Unit\Repository;

use Phauthentic\EventSourcing\Repository\AggregateData;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 *
 */
class AggregateDataTest extends TestCase
{
    public function testConstructorAndGetters()
    {
        $aggregateId = 'test-id';
        $aggregateType = 'TestType';
        $version = 1;
        $events = [new stdClass(), new stdClass()];
        $stream = 'test-stream';

        $aggregateData = new AggregateData($aggregateId, $aggregateType, $version, $events, $stream);

        $this->assertSame($aggregateId, $aggregateData->getAggregateId());
        $this->assertSame($aggregateType, $aggregateData->getAggregateType());
        $this->assertSame($version, $aggregateData->getAggregateVersion());
        $this->assertSame($events, $aggregateData->getDomainEvents());
        $this->assertSame($stream, $aggregateData->getStream());
    }

    public function testConstructorWithDefaults()
    {
        $aggregateId = 'test-id';
        $aggregateType = 'TestType';
        $version = 1;

        $aggregateData = new AggregateData($aggregateId, $aggregateType, $version);

        $this->assertSame($aggregateId, $aggregateData->getAggregateId());
        $this->assertSame($aggregateType, $aggregateData->getAggregateType());
        $this->assertSame($version, $aggregateData->getAggregateVersion());
        $this->assertSame([], $aggregateData->getDomainEvents());
        $this->assertNull($aggregateData->getStream());
    }

    public function testCreateFromArray()
    {
        $data = [
            'aggregateId' => 'test-id',
            'aggregateType' => 'TestType',
            'version' => 1,
            'events' => [new stdClass(), new stdClass()],
            'stream' => 'test-stream',
        ];

        $aggregateData = AggregateData::createFromArray($data);

        $this->assertInstanceOf(AggregateData::class, $aggregateData);
        $this->assertSame($data['aggregateId'], $aggregateData->getAggregateId());
        $this->assertSame($data['aggregateType'], $aggregateData->getAggregateType());
        $this->assertSame($data['version'], $aggregateData->getAggregateVersion());
        $this->assertSame($data['events'], $aggregateData->getDomainEvents());
        $this->assertSame($data['stream'], $aggregateData->getStream());
    }

    public function testCreateFromArrayWithDefaults()
    {
        $data = [
            'aggregateId' => 'test-id',
            'aggregateType' => 'TestType',
            'version' => 1,
        ];

        $aggregateData = AggregateData::createFromArray($data);

        $this->assertInstanceOf(AggregateData::class, $aggregateData);
        $this->assertSame($data['aggregateId'], $aggregateData->getAggregateId());
        $this->assertSame($data['aggregateType'], $aggregateData->getAggregateType());
        $this->assertSame($data['version'], $aggregateData->getAggregateVersion());
        $this->assertSame([], $aggregateData->getDomainEvents());
        $this->assertNull($aggregateData->getStream());
    }
}
