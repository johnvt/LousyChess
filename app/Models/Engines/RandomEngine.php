<?php

namespace App\Models\Engines;

use App\Models\Engine;
use App\Models\Game;

class RandomEngine extends Engine
{
    public $name = 'Random';

    public function move(Game $game)
    {
        $validMoves = $this->orderMoves($game->getValidMoves($this->color));

        // No valid moves left??
        if (count($validMoves) == 0) return null;

        // Can we capture the enemy king?
        foreach ($validMoves as $move) {
            if (strtolower($game->board[$move[1]]) == 'k') {
                return $move;
            }
        }

        // Random move
        $rnd = $this->seedArray[$game->moveCount % count($this->seedArray)];

        return $validMoves[$rnd % count($validMoves)];
    }

}
