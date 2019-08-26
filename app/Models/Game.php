<?php

namespace App\Models;

use App\Models\Engines\RandomEngine;
use Illuminate\Database\Eloquent\Model;

class Game //extends Model
{
    const WHITE = 1;
    const BLACK = -1;
    const STARTING_BOARD = 'kqbnrppppp..........PPPPPRNBQK';

    public $players;
    public $moveCount = 0;
    public $moves;
    public $turn = self::WHITE;
    public $board = self::STARTING_BOARD;
    public $winner;
    public $seed1;
    public $seed2;

    public function run($bombNumber, $seed1, $seed2)
    {
        $this->seed1 = $seed1;
        $this->seed2 = $seed2;
        $this->players = [
            self::WHITE => new RandomEngine(self::WHITE, $bombNumber * $seed1),
            self::BLACK => new RandomEngine(self::BLACK, $bombNumber * $seed2)
        ];

        while (true) {
            if ($this->turn == self::WHITE) {
                $this->moveCount++;
            }
            $move = $this->players[$this->turn]->move($this);
            if (is_null($move)) {
                break;
            }
            $this->moves[] = $move;
            if ($this->board[$move[1]] == 'k') {
                $this->winner = self::WHITE;
            }
            elseif ($this->board[$move[1]] == 'K') {
                $this->winner = self::BLACK;
            }
            $this->board[$move[1]] = $this->board[$move[0]];
            $this->board[$move[0]] = '.';

            // Pawn promotion
            // @todo

            // Do we have a winner?
            if (!is_null($this->winner)) break;

            // Draw by 50 moves?
            if (count($this->moves) == 50) break;

            $this->turn = -$this->turn;
        }
    }

    public function pieceAt($x, $y)
    {
        $piece = $this->board[$x + $y * 5];

        return $piece == '.' ? null : $piece;
    }

    public function getValidMoves($player)
    {
        $moves = [];

        // Scan all squares
        foreach (str_split($this->board) as $i => $piece) {

            // If it's empty, or from the enemy, continue
            $color = $this->colorOf($piece);
            if (is_null($color)) continue;
            if ($color != $player) continue;

            $x = $i % 5;
            $y = intdiv($i, 5);
            $piece = strtolower($piece);
            switch ($piece) {
                case 'k':
                    $moves = array_merge($moves, $this->scanAllDirections($x, $y, 0, 1, true));
                    $moves = array_merge($moves, $this->scanAllDirections($x, $y, 1, 1, true));
                    break;
                case 'q':
                    $moves = array_merge($moves, $this->scanAllDirections($x, $y, 0, 1));
                    $moves = array_merge($moves, $this->scanAllDirections($x, $y, 1, 1));
                    break;
                case 'r':
                    $moves = array_merge($moves, $this->scanAllDirections($x, $y, 0, 1));
                    break;
                case 'b':
                    // Extra MinitChess bishop move:
                    $moves = array_merge($moves, $this->scanAllDirections($x, $y, 0, 1, true, 'no'));
                    $moves = array_merge($moves, $this->scanAllDirections($x, $y, 1, 1));
                    break;
                case 'n':
                    $moves = array_merge($moves, $this->scanAllDirections($x, $y, 1, 2, true));
                    $moves = array_merge($moves, $this->scanAllDirections($x, $y, -1, 2, true));
                    break;
                case 'p':
                    $moves = array_merge($moves, $this->scan($x, $y, -1, -$color, true, 'only'));
                    $moves = array_merge($moves, $this->scan($x, $y, 1, -$color, true, 'only'));
                    $moves = array_merge($moves, $this->scan($x, $y, 0, -$color, true, 'false'));
                    break;
            }
        }

        return $moves;
    }

    /**
     * @param int $x
     * @param int $y
     * @param int $dx
     * @param int $dy
     * @param bool $lastStep
     * @param string $capture
     * @return array
     */
    private function scanAllDirections($x, $y, $dx, $dy, $lastStep = false, string $capture = 'yes')
    {
        $moves = [];
        for ($i = 0; $i < 4; $i++) {
            $moves = array_merge($moves, $this->scan($x, $y, $dx, $dy, $lastStep, $capture));

            // Exchange dx with dy and negate dy
            $temp = $dx;
            $dx = $dy;
            $dy = -$temp;
        }

        return $moves;
    }

    /**
     * @param int $x
     * @param int $y
     * @param int $dx
     * @param int $dy
     * @param bool $lastStep
     * @param string $capture
     * @return array
     */
    private function scan($x, $y, $dx, $dy, $lastStep = false, $capture = 'yes')
    {
        $x0 = $x;
        $y0 = $y;
        $piece = $this->pieceAt($x, $y);
        $color = $this->colorOf($piece);
        $moves = [];
        while (true) {

            // Take a step
            $x += $dx;
            $y += $dy;

            // Out of bounds
            if ($x < 0 || $x > 4 || $y < 0 || $y > 5) {
                break;
            }

            $piece = $this->pieceAt($x, $y);

            // There is a piece
            if (!is_null($piece)) {

                // Own piece
                if ($this->colorOf($piece) == $color) {
                    break;
                }

                // Other piece but we may not capture
                if ($capture == 'no') {
                    break;
                }

                // We may capture, the piece cannot jump over it
                $lastStep = true;
            }

            // No piece, but we may only capture
            elseif ($capture == 'only') {
                break;
            }

            // Add move
            $moves[] = [$this->pos($x0, $y0), $this->pos($x, $y)];

            // Stop if it's the last step
            if ($lastStep) break;
        }

        return $moves;
    }

    private function colorOf($piece)
    {
        if ($piece == '.') return null;

        return $piece == strtoupper($piece) ? self::WHITE : self::BLACK;
    }

    private function pos($x, $y)
    {
        return $x + $y * 5;
    }


    public function getMovesJson()
    {
        $moves = array_merge([[-1, -1]], $this->moves);

        $board = self::STARTING_BOARD;
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
