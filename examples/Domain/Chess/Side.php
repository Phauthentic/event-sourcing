<?php

declare(strict_types=1);

namespace Example\Domain\Chess;

/**
 *
 */
enum Side: string
{
    case WHITE = 'white';
    case BLACK = 'black';
}
