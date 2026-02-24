<?php

/**
 * Copyright (c) Florian Krämer (https://florian-kraemer.net)
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE file
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Florian Krämer (https://florian-kraemer.net)
 * @author    Florian Krämer
 * @link      https://github.com/Phauthentic
 * @license   https://opensource.org/licenses/MIT MIT License
 */

declare(strict_types=1);

namespace Phauthentic\EventSourcing\Repository\SnapshotStrategy;

use Phauthentic\EventSourcing\Repository\AggregateDataInterface;

/**
 *
 */
class EveryNthVersionStrategy implements SnapshotStrategyInterface
{
    public function __construct(
        protected int $modulus = 5
    ) {
    }

    public function isApplicable(AggregateDataInterface $aggregateData): bool
    {
        return $aggregateData->getAggregateVersion() % $this->modulus === 0;
    }
}
