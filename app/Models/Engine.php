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

    /**
     * @param Game $game
     * @return array Move
     */
    abstract public function move(Game $game);

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
}
