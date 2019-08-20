<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Game //extends Model
{
    const STARTING_POSITION = 'kqbnrppppp.+.+.+.+.+PPPPPRNBQK';

    public function getBoardCharacters()
    {
        $string = strtr(self::STARTING_POSITION, 'PNBRQKpnbrqk', 'phbrqkojntwl');

        for ($i = 0; $i < strlen($string); $i++) {
            if ($i % 2) $string[$i] = strtoupper($string[$i]);
        }

        return str_split($string, 5);
    }

    public function getMovesJson() {
        return json_encode([
            ["b2", "b3"],
            ["c5", "c4"]
        ]);
    }
}
