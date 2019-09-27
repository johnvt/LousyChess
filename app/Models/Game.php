<?php

namespace App\Models;

use Chovanec\Rating\Rating;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Game
 *
 * @property int $id
 * @property int $white_engine_id
 * @property int $black_engine_id
 * @property string $serial_number
 * @property string $bomb_number
 * @property int $white_seed
 * @property string $white_random
 * @property int $black_seed
 * @property string $black_random
 * @property int $num_moves
 * @property int|null $winner
 * @property mixed $moves_json
 * @property mixed $boards_json
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Engine $blackEngine
 * @property-read mixed $winner_text
 * @property-read \App\Models\Engine $whiteEngine
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Game newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Game newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Game query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Game whereBlackEngineId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Game whereBlackRandom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Game whereBlackSeed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Game whereBoardsJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Game whereBombNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Game whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Game whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Game whereMovesJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Game whereNumMoves($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Game whereSerialNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Game whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Game whereWhiteEngineId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Game whereWhiteRandom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Game whereWhiteSeed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Game whereWinner($value)
 * @mixin \Eloquent
 */
class Game extends Model
{
    const WHITE = 1;
    const BLACK = -1;
    const STARTING_BOARD = 'kqbnrppppp..........PPPPPRNBQK';

    public $turn = self::WHITE;

    /** @var string Current board */
    public $board = self::STARTING_BOARD;
    public $moves = [];
    public $boards;

    public function whiteEngine()
    {
        return $this->belongsTo(Engine::class);
    }

    public function blackEngine()
    {
        return $this->belongsTo(Engine::class);
    }

    public function run()
    {
        if (!$this->whiteEngine || !$this->blackEngine)
            throw new \Exception('Engines not set');

        $this->boards = [self::STARTING_BOARD];

        // Random serial number
        foreach (str_split('xxnlln') as $char) {
            if ($char == 'x') $char = rand(0, 1) == 0 ? 'n' : 'l';
            if ($char == 'n') $this->serial_number .= rand(0, 9);
            else $this->serial_number .= chr(rand(ord('A'), ord('Z')));
        };
        foreach (str_split($this->serial_number) as $char) {
            if (is_numeric($char)) $this->bomb_number .= $char;
            else $this->bomb_number .= ord($char) - ord('A') + 1;
        }

        // Random seeds
        $this->white_seed = rand(0, 9);
        do {
            $this->black_seed = rand(0, 9);
        } while ($this->black_seed == $this->white_seed);

        // Init engines
        $this->white_random = $this->whiteEngine->init(self::WHITE, $this->white_seed, $this->bomb_number);
        $this->black_random = $this->blackEngine->init(self::BLACK, $this->black_seed, $this->bomb_number);

        // Take turns until game is finished
        while (true) {
            if ($this->turn == self::WHITE) {
                $this->num_moves++;
            }

            $engine = ($this->turn == self::WHITE) ? $this->whiteEngine : $this->blackEngine;
            $move = $engine->move($this);

            // No valid moves left, other player wins
            if (is_null($move)) {
                $this->winner = ($this->turn == self::WHITE) ? self::BLACK : self::WHITE;
                break;
            }

            // Valid move
            $this->moves[] = $move;

            // King captured? Game over
            if ($this->board[$move[1]] == 'k') {
                $this->winner = self::WHITE;
            }
            elseif ($this->board[$move[1]] == 'K') {
                $this->winner = self::BLACK;
            }
            $this->board[$move[1]] = $this->board[$move[0]];
            $this->board[$move[0]] = '.';

            // Is the game finished?
            if (!is_null($this->winner)) {
                $this->boards[] = $this->board;
                break;
            }

            // Pawn promotion
            if (intdiv($move[1], 5) == 0 && $this->board[$move[1]] == 'P') {
                $this->board[$move[1]] = 'Q';
            }
            elseif (intdiv($move[1], 5) == 5 && $this->board[$move[1]] == 'p') {
                $this->board[$move[1]] = 'q';
            }

            $this->boards[] = $this->board;

            // Draw by 40 moves by each side
            if ($this->turn == self::BLACK && $this->num_moves == 40) {
                break;
            }

            $this->turn = -$this->turn;
        }

        // Adjust Elo rating
        $rating = (new Rating(
            $this->whiteEngine->elo_rating,
            $this->blackEngine->elo_rating,
            $this->winner ? ($this->winner == self::WHITE ? Rating::WIN : Rating::LOST) : Rating::DRAW,
            $this->winner ? ($this->winner == self::BLACK ? Rating::WIN : Rating::LOST) : Rating::DRAW
        ))->getNewRatings();

        if (!$this->whiteEngine->is($this->blackEngine)) {
            $this->whiteEngine->elo_rating = $rating['a'];
            $this->whiteEngine->save();
            $this->blackEngine->elo_rating = $rating['b'];
            $this->blackEngine->save();
        }

        $this->moves_json = json_encode($this->moves);
        $this->boards_json = json_encode($this->boards);
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
                    $moves = array_merge($moves, $this->scan($x, $y, 0, -$color, true, 'no'));
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

    public function pos($x, $y)
    {
        return $x + $y * 5;
    }

    public function xy($pos)
    {
        return [$pos % 5, intdiv($pos, 5)];
    }

    public function getMovesJson()
    {
        $moves = array_merge([[-1, -1]], json_decode($this->moves_json));
        $boards = json_decode($this->boards_json);

        $result = [];
        foreach ($moves as $i => $move) {

            // Piece chars to font chars
            $string = strtr($boards[$i], 'PNBRQKpnbrqk', 'phbrqkojntwl');
            for ($i = 0; $i < strlen($string); $i++) {
                $string[$i] = ($i % 2)
                    ? ($string[$i] == '.' ? '+' : strtoupper($string[$i]))
                    : strtolower($string[$i]);
            }
            $result[] = ['from' => $move[0], 'to' => $move[1], 'board' => $string];
        }

        return json_encode($result, JSON_PRETTY_PRINT);
    }

    public function getDistance($from, $to)
    {
        $fromXy = $this->xy($from);
        $toXy = $this->xy($to);
        return abs($fromXy[0] - $toXy[0]) + abs($fromXy[1] - $toXy[1]);
    }

    public function getWinnerTextAttribute()
    {
        if ($this->winner == $this::WHITE) return 'White';
        if ($this->winner == $this::BLACK) return 'Black';
        return 'Draw';
    }
}
