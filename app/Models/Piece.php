<?php

namespace App\Models;


class Piece
{
    static public function isKing($piece)
    {
        return strtolower($piece) == 'k';
    }

    static public function isQueen($piece)
    {
        return strtolower($piece) == 'q';
    }

    static public function isRook($piece)
    {
        return strtolower($piece) == 'r';
    }

    static public function isBishop($piece)
    {
        return strtolower($piece) == 'b';
    }

    static public function isKnight($piece)
    {
        return strtolower($piece) == 'n';
    }

    static public function isPawn($piece)
    {
        return strtolower($piece) == 'p';
    }

    public static function isBlack($piece)
    {
        return $piece == strtolower($piece);
    }

    public static function isWhite($piece)
    {
        return $piece == strtoupper($piece);
    }

    public static function otherColor($piece)
    {
        return self::isWhite($piece) ? strtolower($piece) : strtoupper($piece);
    }
}