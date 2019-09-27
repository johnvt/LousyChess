@extends('layouts.app')

@php
    /* @var App\Models\Engine[] $engines */
    /* @var App\Models\Game[] $games */
@endphp

@section('content')
    <div class="row">
        <h3>Total games played: {{ $numGames }}</h3>
        <table class="table table-sm table-hover">
            <tr>
                <th>Engine</th>
                <th>Elo rating</th>
            </tr>
            @foreach ($engines as $engine)
                <tr>
                    <td>{{ $engine->name }}</td>
                    <td>{{ $engine->elo_rating }}</td>
                </tr>
            @endforeach
        </table>
        <table class="table table-sm table-hover">
            <tr>
                <th>White</th>
                <th>Black</th>
                <th class="text-right">Average number of moves</th>
                <th class="text-right">White wins</th>
                <th class="text-right">Black wins</th>
                <th class="text-right">Draws</th>
            </tr>
            @foreach ($games as $game)
                @php $class = !$game->whiteEngine ? 'table-primary' : (!$game->blackEngine ? 'table-secondary' : '') @endphp
                <tr class="{{ $class }}">
                    <td>{{ $game->whiteEngine->name ?? 'all' }}</td>
                    <td>{{ $game->blackEngine->name ?? 'all' }}</td>
                    <td class="text-right">{{ round($game->avg_num_moves, 1) }}</td>
                    <td class="text-right">{{ round($game->num_white_wins / $game->num_games * 100) }} %</td>
                    <td class="text-right">{{ round($game->num_black_wins / $game->num_games * 100) }} %</td>
                    <td class="text-right">{{ round($game->num_draws / $game->num_games * 100) }} %</td>
                </tr>
            @endforeach
        </table>
    </div>
@endsection