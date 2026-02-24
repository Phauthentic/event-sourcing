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

namespace Phauthentic\EventSourcing\DomainEvent\Attribute;

use Attribute;
use DateTimeImmutable;

/**
 *
 */
#[Attribute(Attribute::TARGET_CLASS)]
class EventCreatedAt
{
    protected DateTimeImmutable $value;

    public function __construct()
    {
        $this->value = new DateTimeImmutable();
    }
}
