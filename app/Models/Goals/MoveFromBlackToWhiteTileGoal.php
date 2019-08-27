<?php

namespace App\Models\Goals;

use App\Models\Game;
use App\Models\Goal;

class MoveFromBlackToWhiteTileGoal extends Goal
{
    /**
     * @param $moves
     * @param Game $game
     * @return array Moves
     */
    public function filter($moves, Game $game)
    {
        return collect($moves)
            ->filter(function ($move) use ($game) {
                return ($move[0] % 2 == 1) && ($move[1] % 2 == 0);
            })
            ->all();
    }
}

