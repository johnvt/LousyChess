<?php

namespace App\Http\Controllers;

use App\Models\Engine;
use App\Models\Game;
use DB;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;

class EngineController extends Controller
{
    public function index(Request $request)
    {
        $engines = Engine::orderBy('elo_rating', 'desc')->get();

        $games = Game
            ::with(['whiteEngine', 'blackEngine'])
            ->groupBy(DB::raw('white_engine_id, black_engine_id with rollup'))
            ->select([
                'white_engine_id',
                'black_engine_id',
                DB::raw('sum(if(winner=1, 1, 0)) num_white_wins'),
                DB::raw('sum(if(winner=-1, 1, 0)) num_black_wins'),
                DB::raw('sum(if(winner is null, 1, 0)) num_draws'),
                DB::raw('avg(num_moves) avg_num_moves'),
                DB::raw('count(*) num_games')
            ])
            ->when($request->has('sort'), function (Builder $q) use ($request) {
                return $q->orderBy($request->get('sort'), $request->get('dir'));
            })
            ->get();

        $numGames = Game::count();

        return view('engine.index', ['engines' => $engines, 'games' => $games, 'numGames' => $numGames]);
    }
}
