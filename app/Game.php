<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Game //extends Model
{
    public function getBoardCharacters($board)
    {
        $string = str_replace(
            str_split('PNBRQKpnbrqk'),
            str_split('phbrqkojntwl'),
            $board);

        $result = [];
        for ($i = 0; $i < strlen($string); $i++) {
            $result[] = $i % 2 ? $string[$i] : strtoupper($string[$i]);
        }

        return $result;
    }
}
