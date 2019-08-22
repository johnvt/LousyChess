<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Game //extends Model
{
    const STARTING_POSITION = 'kqbnrppppp.+.+.+.+.+PPPPPRNBQK';

    public function getMoves()
    {
        return [
            [21, 16],
            [7, 12]
        ];
    }

    public function getMovesJson()
    {
        $board = self::STARTING_POSITION;
        $moves = array_merge([[-1, -1]], $this->getMoves());

        $result = [];
        foreach ($moves as $move) {
            if ($move[0] != -1) {
                $board[$move[1]] = $board[$move[0]];
                $board[$move[0]] = '.';
            }

            // Piece chars to font chars
            $string = strtr($board, 'PNBRQKpnbrqk', 'phbrqkojntwl');
            for ($i = 0; $i < strlen($string); $i++) {
                $string[$i] = ($i % 2)
                    ? ($string[$i] == '.' ? '+' : strtoupper($string[$i]))
                    : strtolower($string[$i]);
            }
            $result[] = ['from' => $move[0], 'to' => $move[1], 'board' => $string];
        }
        return json_encode($result, JSON_PRETTY_PRINT);
    }
}
