<?php

namespace App\Models\Goals;

use App\Models\Game;
use App\Models\Goal;

class MoveCloserToKingGoal extends Goal
{
    /**
     * @param $moves
     * @param Game $game
     * @return array Moves
     */
    public function filter($moves, Game $game)
    {
        $enemyKing = ($game->turn == $game::WHITE) ? 'k' : 'K';
        $enemyKingSquare = strpos($game->board, $enemyKing);

        return collect($moves)
            ->filter(function ($move) use ($game, $enemyKingSquare) {
                $oldDistance = $game->getDistance($move[0], $enemyKingSquare);
                $newDistance = $game->getDistance($move[1], $enemyKingSquare);
                return $newDistance < $oldDistance;
            })
            ->all();
    }
}

