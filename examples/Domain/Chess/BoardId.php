<?php

declare(strict_types=1);

namespace Example\Domain\Chess;

/**
 *
 */
class BoardId
{
    public function __construct(
        public readonly string $id
    )
    {
    }
}
