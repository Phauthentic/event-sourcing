<?php

declare(strict_types=1);

namespace Example\Domain\Chess;

/**
 *
 */
enum PieceType: string
{
    case PAWN = 'p';
    case QUEEN = 'Q';
    case ROOK = 'R';
    case BISHOP = 'B';
    case KNIGHT = 'N';
    case KING = 'K';
}
