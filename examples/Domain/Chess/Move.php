<?php

declare(strict_types=1);

namespace Example\Domain\Chess;

/**
 *
 */
class Move
{
    public function assertValidField(): string
    {
        return '/^([a-h][1-8])\s*-\s*([a-h][1-8])$/';
    }
}
