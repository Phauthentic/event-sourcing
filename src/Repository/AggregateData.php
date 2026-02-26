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

/**
 * An agnostic aggregate data transfer.
 */
class AggregateData implements AggregateDataInterface
{
    /**
     * @param string $aggregateId
     * @param string $aggregateType
     * @param int $version
     * @param array<int, object> $events
     * @param string|null $stream
     */
    public function __construct(
        protected string $aggregateId,
        protected string $aggregateType,
        protected int $version,
        protected array $events = [],
        protected ?string $stream = null
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function createFromArray(array $data): AggregateDataInterface
    {
        return new self(...$data);
    }

    public function getAggregateId(): string
    {
        return $this->aggregateId;
    }

    public function getAggregateType(): string
    {
        return $this->aggregateType;
    }

    public function getAggregateVersion(): int
    {
        return $this->version;
    }

    /**
     * @return array<int, object>
     */
    public function getDomainEvents(): array
    {
        return $this->events;
    }

    public function getStream(): ?string
    {
        return $this->stream;
    }
}
