<?php

namespace App\Models;

abstract class Engine
{
    public $seed;
    public $seedArray;
    public $color;

    public function __construct($color, $seed)
    {
        $this->color = $color;
        $this->seed = $seed;
        $this->seedArray = array_map('intval', str_split($seed));
    }

    public function move(Game $game)
    {
        $validMoves = $this->orderMoves($game->getValidMoves($this->color));

        // No valid moves left??
        if (count($validMoves) == 0) return null;

        // Try each goal
        foreach ($this->getGoals() as $goal) {
            $moves = $goal->filter($validMoves, $game);
            if (count($moves)) {
                return $this->getRandomMove($moves, $game);
            }
        }

        // No goal can be met? Pick any valid move.
        return $this->getRandomMove($validMoves, $game);
    }

    public function orderMoves(array $moves)
    {
        $indexedMoves = [];
        foreach ($moves as $key => $move) {
            $index = chr(ord('a') + $move[0] % 5) .
                strval(6 - intdiv($move[0], 5)) .
                chr(ord('a') + $move[1] % 5) .
                strval(6 - intdiv($move[1], 5));
            $indexedMoves[$index] = $move;
        }
        ksort($indexedMoves);

        return array_values($indexedMoves);
    }

    /**
     * @return Goal[]
     */
    abstract public function getGoals();

    public function getRandomMove($moves, $game)
    {
        if (count($moves) == 1) {
            return reset($moves);
        }

        $moves = array_values($moves);
        $rnd = $this->seedArray[$game->moveCount % count($this->seedArray)];

        return $moves[$rnd % count($moves)];
    }
}
