@extends('layouts.app')

@php /* @var App\Models\Game[] $games */ @endphp

@section('content')

    <div class="row">

        <table class="table table-sm table-hover">
            <tr>
                <th>
                    Serial number<br>
                    @include('sort', ['field' => 'serial_number'])
                </th>
                <th>
                    White<br>
                    @include('sort', ['field' => 'white_engine_id'])
                </th>
                <th>
                    Black<br>
                    @include('sort', ['field' => 'black_engine_id'])
                </th>
                <th>
                    Winner<br>
                    @include('sort', ['field' => 'winner'])
                </th>
                <th class="text-right">
                    Number of moves<br>
                    @include('sort', ['field' => 'num_moves'])
                </th>
                <th class="text-right">
                    White seed<br>
                    @include('sort', ['field' => 'white_seed'])
                </th>
                <th class="text-right">
                    White random<br>
                    @include('sort', ['field' => 'white_random'])
                </th>
                <th class="text-right">
                    Black seed<br>
                    @include('sort', ['field' => 'black_seed'])
                </th>
                <th class="text-right">
                    Black random<br>
                    @include('sort', ['field' => 'black_random'])
                </th>
            </tr>
            @foreach ($games as $game)
                <tr class="link" data-url="{{ route('games.show', ['id' => $game->id]) }}">
                    <td>{{ $game->serial_number }}</td>
                    <td>{{ $game->whiteEngine->name }}</td>
                    <td>{{ $game->blackEngine->name }}</td>
                    <td>{{ $game->winnerText }}</td>
                    <td class="text-right">{{ $game->num_moves }}</td>
                    <td class="text-right">{{ $game->white_seed }}</td>
                    <td class="text-right">{{ $game->white_random }}</td>
                    <td class="text-right">{{ $game->black_seed }}</td>
                    <td class="text-right">{{ $game->black_random }}</td>
                </tr>
            @endforeach
        </table>
    </div>

    <div class="row  justify-content-md-center">
        {{ $games->links() }}
    </div>

@endsection

@section('scripts')
    <script>
        $(function () {
            $('tr.link').on('click', function () {
                window.location = $(this).data('url');
            });
        });
    </script>
@endsection