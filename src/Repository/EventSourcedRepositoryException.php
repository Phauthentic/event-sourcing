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

namespace Phauthentic\EventSourcing\Repository;

use Phauthentic\EventSourcing\EventSourcingException;

/**
 *
 */
class EventSourcedRepositoryException extends EventSourcingException
{
    public static function couldNotReconstituteAggregate(string $aggregateType): self
    {
        return new self(sprintf(
            'Could not reconstitute aggregate of type: %s',
            $aggregateType
        ));
    }

    public static function missingReconstitutionMethod(string $aggregateType, string $methodName): self
    {
        return new self(sprintf(
            'Aggregate class `%s` does not have a method `%s` to reconstruct the aggregate state.',
            $aggregateType,
            $methodName
        ));
    }
}
