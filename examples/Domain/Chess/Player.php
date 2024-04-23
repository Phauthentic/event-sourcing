<?php

declare(strict_types=1);

namespace Example\Domain\Chess;

/**
 *
 */
class Player
{
    public function __construct(
        public readonly string $name,
        public readonly Side $side
    )
    {
    }
}
