<?php

namespace App\Http\Controllers;

use App\Models\Engine;
use App\Models\Game;
use Illuminate\Http\Request;

class GameController extends Controller
{
    public function seeds()
    {
        echo "<table border='1'>";
        foreach (Game::inRandomOrder()->limit(10)->get() as $game) {
            $bombNumber = "";
            foreach (str_split($game->serial_number) as $char) {
                if (is_numeric($char)) $bombNumber .= $char;
                else $bombNumber .= ord($char) - ord('A') + 1;
            }
            for ($seed = 0; $seed < 10; $seed++) {
                $rng = $random = $seed;
                for ($i = 0; $i < 100; $i++) {
                    $random = ($random + $bombNumber[$i % strlen($bombNumber)]) % 10;
                    $rng .= $random;
                }
                $counts = [];
                for ($i = 0; $i < 10; $i++) $counts[$i] = 0;
                foreach (str_split($rng) as $char) {
                    $counts[$char]++;
                }
                echo "<tr><td>{$seed}</td><td>{$bombNumber}</td><td>{$rng}</td>";
                foreach ($counts as $count) echo "<td>{$count}</td>";
                echo "</tr>";
            }
        }
        echo "</table>";
    }

    public function index(Request $request)
    {
        $games = Game::query();
        if ($request->has('sort') && $request->has('dir')) {
            $games->orderBy($request->get('sort'), $request->get('dir'));
        }
        $games = $games->paginate(20);

        return view('game.index', ['games' => $games]);
    }

    public function create()
    {
        ini_set('max_execution_time', 60 * 60);
        for ($i = 0; $i < 100; $i++) {
            foreach (Engine::all() as $white) {
                foreach (Engine::all() as $black) {
                    $game = new Game;
                    $game->whiteEngine()->associate($white);
                    $game->blackEngine()->associate($black);
                    $game->run();
                    $game->save();
                }
            }
        }
    }

    public function run()
    {
        $game = new Game;
        $engines = Engine::inRandomOrder()->limit(2)->get();
        $game->whiteEngine()->associate($engines[0]);
        $game->blackEngine()->associate(Engine::whereCode('M')->first());
        $game->run();
        $game->save();
        return view('game.show', ['game' => $game]);
    }

    public function show(Game $game)
    {
        return view('game.show', ['game' => $game]);
    }
}
