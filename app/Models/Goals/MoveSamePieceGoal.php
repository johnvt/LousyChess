<?php

namespace App\Models\Goals;

use App\Models\Game;
use App\Models\Goal;
use App\Models\Piece;

class MoveSamePieceGoal extends Goal
{
    /**
     * @param $moves
     * @param Game $game
     * @return array Moves
     */
    public function filter($moves, Game $game)
    {
        if (!count($game->moves)) return collect();

        $prevMove = last($game->moves);
        $prevPiece = $game->board[$prevMove[1]];
        $piece = Piece::otherColor($prevPiece);

        return collect($moves)
            ->filter(function ($move) use ($game, $piece) {
                return $game->board[$move[0]] == $piece;
            })
            ->all();
    }
}

