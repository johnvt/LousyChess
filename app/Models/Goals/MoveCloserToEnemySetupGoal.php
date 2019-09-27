<?php

namespace App\Models\Goals;

use App\Models\Game;
use App\Models\Goal;
use App\Models\Piece;

class MoveCloserToEnemySetupGoal extends Goal
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

                $piece = $game->board[$move[0]];
                if (Piece::isPawn($piece)) {
                    $enemySquare = $game->pos(
                        $game->xy($move[0])[0],
                        Piece::isBlack($piece) ? 4 : 1
                    );
                }
                else {
                    $enemySquare = strpos($game::STARTING_BOARD, Piece::otherColor($piece));
                }

                $oldDistance = $game->getDistance($move[0], $enemySquare);
                $newDistance = $game->getDistance($move[1], $enemySquare);
                return $newDistance < $oldDistance;
            })
            ->all();
    }
}

