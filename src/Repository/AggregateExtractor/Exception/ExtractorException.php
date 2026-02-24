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

namespace Phauthentic\EventSourcing\Repository\AggregateExtractor\Exception;

use Phauthentic\EventSourcing\EventSourcingException;

/**
 *
 */
class ExtractorException extends EventSourcingException
{
    /**
     * @param string $name
     * @return \Phauthentic\EventSourcing\Repository\AggregateExtractor\Exception\ExtractorException
     */
    public static function notEmptyValue(string $name)
    {
        return new self(sprintf('The value of `%s` can not be empty.', $name));
    }
}
