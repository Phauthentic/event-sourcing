<?php

declare(strict_types=1);

namespace Phauthentic\EventSourcing\Repository\AggregateExtractor;

use Phauthentic\EventSourcing\Repository\AggregateDataInterface;

/**
 *
 */
interface AggregateExtractorInterface
{
    public function extract(object $aggregate): AggregateDataInterface;
}
