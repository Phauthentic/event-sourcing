<?php

declare(strict_types=1);

namespace Example\Domain\Chess;

/**
 *
 */
class Position
{
    public function __construct(
        public readonly string $position,
    )
    {
        $this->assertValidField($position);
    }

    public function assertValidField(): string
    {
        return '/^([a-h][1-8])\s*-\s*([a-h][1-8])$/';
    }

    public function __toString(): string
    {
        return $this->position;
    }

    public function toString()
    {
        return $this->position;
    }
}
