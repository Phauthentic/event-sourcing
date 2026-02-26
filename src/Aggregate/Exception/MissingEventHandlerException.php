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

namespace Phauthentic\EventSourcing\Aggregate\Exception;

/**
 *
 */
class MissingEventHandlerException extends AggregateException
{
    protected const WITH_NAME_AND_CLASS_MESSAGE = 'Handler method `%s` for event `%s` does not exist in aggregate `%s`';

    /**
     * @param string $eventName
     * @param string $eventClass
     * @param string $aggregateClass
     * @return MissingEventHandlerException
     */
    public static function withNameAndClass(
        string $eventName,
        string $eventClass,
        string $aggregateClass
    ): self {
        return new self(sprintf(
            self::WITH_NAME_AND_CLASS_MESSAGE,
            $eventName,
            $eventClass,
            $aggregateClass
        ));
    }
}
