<?php

declare(strict_types=1);

namespace Phauthentic\EventSourcing\Repository\AggregateExtractor;

use Phauthentic\EventSourcing\Repository\AggregateDataInterface;

/**
 * An aggregate extrator is responsible for extracting the relevant data from an aggregate object.
 *
 * It extracts the following items from an aggregate object:
 * - aggregate id
 * - aggregate version
 * - domain events
 * - aggregate type
 *
 * The extracted data is returned as a normalized data object.
 *
 * The purpose of this interface is to provide a way to extract the relevant data from an aggregate object to abstract
 * working with the relevant data from the aggregate within the repository.
 */
interface AggregateExtractorInterface
{
    /**
     * Extracts the relevant data from an aggregate object.
     *
     * @param object $aggregate
     * @return AggregateDataInterface
     */
    public function extract(object $aggregate): AggregateDataInterface;
}
