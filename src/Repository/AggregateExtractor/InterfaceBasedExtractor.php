<?php

declare(strict_types=1);

namespace Phauthentic\EventSourcing\Repository\AggregateExtractor;

use Phauthentic\EventSourcing\Aggregate\EventSourcedAggregateInterface;
use Phauthentic\EventSourcing\Aggregate\TypeProvidingAggregateInterface;
use Phauthentic\EventSourcing\Repository\AggregateData;
use Phauthentic\EventSourcing\Repository\AggregateDataInterface;
use Phauthentic\EventSourcing\Repository\AggregateExtractor\Exception\ExtractorException;

/**
 *
 */
class InterfaceBasedExtractor implements AggregateExtractorInterface
{
    public function extract(object $aggregate): AggregateDataInterface
    {
        if (!$aggregate instanceof EventSourcedAggregateInterface) {
            throw new ExtractorException();
        }

        return new AggregateData(
            aggregateId: $aggregate->getAggregateId(),
            aggregateType: $this->getAggregateTypeFromAggregate($aggregate),
            version: $aggregate->getAggregateVersion(),
            events: $aggregate->consumeAggregateEvents()
        );
    }

    protected function getAggregateTypeFromAggregate(object $aggregate): string
    {
        if ($aggregate instanceof TypeProvidingAggregateInterface) {
            return $aggregate->getAggregateType();
        }

        return get_class($aggregate);
    }
}
