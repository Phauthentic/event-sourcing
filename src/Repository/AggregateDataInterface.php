<?php

declare(strict_types=1);

namespace Phauthentic\EventSourcing\Repository;

/**
 *
 */
interface AggregateDataInterface
{
    /**
     * @return string
     */
    public function getAggregateId(): string;

    /**
     * @return string
     */
    public function getAggregateType(): string;

    /**
     * @return int
     */
    public function getAggregateVersion(): int;

    /**
     * @return array<int, object>
     */
    public function getDomainEvents(): array;

    /**
     * @return null|string
     */
    public function getStream(): ?string;
}
