<?php

declare(strict_types=1);

namespace Example\Domain\Chess;

use Exception;
use Phauthentic\EventSourcing\Aggregate\Attribute\AggregateIdentifier;
use Phauthentic\EventSourcing\Aggregate\Attribute\AggregateVersion;
use Phauthentic\EventSourcing\Aggregate\Attribute\DomainEvents;

/**
 * @link https://en.wikipedia.org/wiki/Algebraic_notation_(chess)
 */
class Board
{
    #[AggregateIdentifier]
    private BoardId $boardId;

    #[DomainEvents]
    private array $domainEvents = [];

    #[AggregateVersion]
    private int $aggregateVersion = 0;

    private array $fields = [];

    private array $pieces = [];

    private Player $activePlayer;

    public function __construct(
        BoardId $boardId,
        private Player $playerOne,
        private Player $playerTwo
    ) {
        $this->assertPlayersDonNotHaveTheSameSide($playerOne, $playerTwo);

        $this->boardId = $boardId;
        $this->playerOne = $playerOne;
        $this->playerTwo = $playerTwo;

        $this->setPieces();
        $this->randomPlayerSelection();

        $this->recordThat(new BoardCreated($boardId, $playerOne, $playerTwo));
    }

    private function randomPlayerSelection()
    {
        $this->activePlayer = rand(0, 1) === 0 ? $this->playerOne : $this->playerTwo;
    }

    private function setPieces()
    {
        // Generate pawn pieces
        $charCode = 96;
        for ($i = 0; $i < 8; $i++) {
            $charCode++;
            $this->fields[chr($charCode) . 7] = new Piece(Side::BLACK, PieceType::PAWN, new Position(chr($charCode) . 7));
            $this->fields[chr($charCode) . 2] = new Piece(Side::WHITE, PieceType::PAWN, new Position(chr($charCode) . 2));
        }

        // Black pieces
        $this->fields['a8'] = new Piece(Side::BLACK, PieceType::ROOK, new Position('a8'));
        $this->fields['h8'] = new Piece(Side::BLACK, PieceType::ROOK, new Position('a8'));
        $this->fields['b8'] = new Piece(Side::BLACK, PieceType::BISHOP, new Position('b8'));
        $this->fields['g8'] = new Piece(Side::BLACK, PieceType::BISHOP, new Position('g8'));
        $this->fields['c8'] = new Piece(Side::BLACK, PieceType::KNIGHT, new Position('c8'));
        $this->fields['f8'] = new Piece(Side::BLACK, PieceType::KNIGHT, new Position('f8'));
        $this->fields['f1'] = new Piece(Side::WHITE, PieceType::QUEEN, new Position('8d'));
        $this->fields['f1'] = new Piece(Side::WHITE, PieceType::KING, new Position('8e'));

        // White pieces
        $this->fields['a1'] = new Piece(Side::WHITE, PieceType::ROOK, new Position('a8'));
        $this->fields['h1'] = new Piece(Side::WHITE, PieceType::ROOK, new Position('a8'));
        $this->fields['b1'] = new Piece(Side::WHITE, PieceType::BISHOP, new Position('b1'));
        $this->fields['g1'] = new Piece(Side::WHITE, PieceType::BISHOP, new Position('g1'));
        $this->fields['c1'] = new Piece(Side::WHITE, PieceType::KNIGHT, new Position('c1'));
        $this->fields['f1'] = new Piece(Side::WHITE, PieceType::KNIGHT, new Position('f1'));
        $this->fields['f1'] = new Piece(Side::WHITE, PieceType::QUEEN, new Position('1e'));
        $this->fields['f1'] = new Piece(Side::WHITE, PieceType::KING, new Position('1d'));
    }

    private function assertPlayersDonNotHaveTheSameSide(Player $playerWhite, Player $playerBlack): void
    {
        assert($playerWhite->side === $playerBlack->side, 'Players must not have the same side!');
    }

    public function move(Position $from, Position $to): void
    {
        foreach ($this->pieces as $piece) {
            if ($piece->position->equals($from)) {
                $this->assertActivePlayerHasThePiece($piece);

                $piece->move($to);
                $this->endTurn();

                return;
            }
        }
    }

    private function fieldHasPawn(Position $position): ?Piece
    {
        if (isset($this->fields[$position->toString()])) {
            return $this->fields[$position->toString()];
        }
    }

    private function endTurn()
    {
        $this->activePlayer = $this->activePlayer === $this->playerOne ? $this->playerTwo : $this->playerOne;
    }

    private function assertActivePlayerHasThePiece(Piece $piece): void
    {
        if ($this->activePlayer->side !== $piece->side) {
            throw new Exception('It is not your turn!');
        }
    }

    private function recordThat(object $event): void
    {
        $this->domainEvents[] = $event;
    }
}
