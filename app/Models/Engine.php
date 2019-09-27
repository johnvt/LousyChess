<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Engine
 *
 * @property int $id
 * @property string $code
 * @property string $name
 * @property mixed $goals_json
 * @property int $elo_rating
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Engine newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Engine newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Engine query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Engine whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Engine whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Engine whereEloRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Engine whereGoalsJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Engine whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Engine whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Engine whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Engine extends Model
{
    public $color;
    public $random = "";

    public function init($color, $seed, string $bombNumber)
    {
        $this->color = $color;
        $this->random = $prev = $seed;
        for ($i = 0; $i < 40; $i++) {
            $next = ($prev + (int)($bombNumber[$i % strlen($bombNumber)])) % 10;
            $this->random .= $next;
            $prev = $next;
        }
        return $this->random;
    }

    public function move(Game $game)
    {
        $validMoves = $this->orderMoves($game->getValidMoves($this->color));

        // No valid moves left??
        if (count($validMoves) == 0) return null;

        // Try each goal
        foreach (json_decode($this->goals_json) as $goalName) {
            $goalName = __NAMESPACE__ . '\\Goals\\' . $goalName;
            /** @var Goal $goal */
            $goal = new $goalName;
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

    public function getRandomMove($moves, Game $game)
    {
        if (count($moves) == 1) {
            return reset($moves);
        }

        $moves = array_values($moves);
        $rnd = $this->random[$game->num_moves];
        $prevRnd = $this->random[$game->num_moves - 1];
        if ($prevRnd % 2 == 0)
            return $moves[$rnd % count($moves)];
        else
            return $moves[count($moves) - 1 - ($rnd % count($moves))];
    }
}
