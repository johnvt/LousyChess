<?php

namespace App\Models\Goals;

use App\Models\Goal;
use App\Models\Game;

class CaptureKingGoal extends Goal
{
    /**
     * @param $moves
     * @param Game $game
     * @return array Moves
     */
    public function filter($moves, Game $game)
    {
        return collect($moves)->filter(function ($move) use ($game) {
            return strtolower($game->board[$move[1]]) == 'k';
        })->all();
    }
}

