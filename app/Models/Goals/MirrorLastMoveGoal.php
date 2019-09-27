<?php

namespace App\Models\Goals;

use App\Models\Game;
use App\Models\Goal;

class MirrorLastMoveGoal extends Goal
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
        $movePiece = $game->board[$prevMove[1]];
        $prevMoveFromXy = $game->xy($prevMove[0]);
        $prevMoveToXy = $game->xy($prevMove[1]);
        $moveFromPos = $game->pos(4 - $prevMoveFromXy[0], 5 - $prevMoveFromXy[1]);
        $moveToPos = $game->pos(4 - $prevMoveToXy[0], 5 - $prevMoveToXy[1]);

        return collect($moves)
            ->filter(function ($move) use ($game, $movePiece, $moveFromPos, $moveToPos) {
                return strtolower($game->board[$move[0]]) == strtolower($movePiece)
                    && $move[0] == $moveFromPos
                    && $move[1] == $moveToPos;
            })
            ->all();
    }
}

