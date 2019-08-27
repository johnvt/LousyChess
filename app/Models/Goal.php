<?php

namespace App\Models;

abstract class Goal
{
    /**
     * @param $moves
     * @param Game $game
     * @return array Move
     */
    abstract public function filter($moves, Game $game);
}
