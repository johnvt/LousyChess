<?php
/** @var App\Models\Game $game */
?>
@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-sm">
            <h4>Serial number: {{ $game->serial_number }}</h4>
            <h4>Bomb number: {{ $game->bomb_number }}</h4>
            <h4>White: {{ $game->whiteEngine->name }}, seed = {{ $game->white_seed }}</h4>
            <h4>Black: {{ $game->blackEngine->name }}, seed = {{ $game->black_seed }}</h4>
            RNG:<br>
            <pre>M#:          1111111111222222222233333333334<br>   -1234567890123456789012345678901234567890<br>W: {{ $game->white_random }}<br>B: {{ $game->black_random }}</pre>
            <table class="chess-diagram table-borderless">
                <tr>
                    <td class="board-border">6</td>
                    <td colspan="5" rowspan="6">
                        <table class="board">
                            @php $light = true @endphp
                            @for ($i = 0; $i < 6; $i++)
                                <tr>
                                    @for ($j = 0; $j < 5; $j++)
                                        <td id="square_{{ $i * 5 + $j }}">{{ $light ? '.' : '+' }} </td>
                                        @php $light = !$light @endphp
                                    @endfor
                                </tr>
                            @endfor
                        </table>
                    </td>
                </tr>
                <tr>
                    <td class="board-border">5</td>
                </tr>
                <tr>
                    <td class="board-border">4</td>
                </tr>
                <tr>
                    <td class="board-border">3</td>
                </tr>
                <tr>
                    <td class="board-border">2</td>
                </tr>
                <tr>
                    <td class="board-border">1</td>
                </tr>
                <tr>
                    <td class="board-border"></td>
                    <td class="board-border">a</td>
                    <td class="board-border">b</td>
                    <td class="board-border">c</td>
                    <td class="board-border">d</td>
                    <td class="board-border">e</td>
                </tr>
            </table>
        </div>
        <div class="col-sm text-center">
            <a href="#" id="prev" class="arrow">&larr;</a>
            &nbsp;&nbsp;&nbsp;
            <a href="#" id="next" class="arrow">&rarr;</a>
            <div class="moves"></div>
            <div class="result">{{ is_null($game->winner) ? 'Draw' : ($game->winner == $game::WHITE ? 'White wins' : 'Black wins') }}</div>
        </div>
    </div>
    <script>

        $(function () {
            var moves = {!! $game->getMovesJson() !!};
            var str = '';
            $.each(moves, function (k, v) {
                if (v.from === -1)
                    str += 'start<br>';
                else {
                    if (k % 2 === 1)
                        str += Math.ceil(k / 2) + '. ';
                    str += '<span id="move_' + k + '" class="move">'
                        + String.fromCharCode('a'.charCodeAt(0) + v.from % 5)
                        + (6 - Math.floor(v.from / 5))
                        + String.fromCharCode('a'.charCodeAt(0) + v.to % 5)
                        + (6 - Math.floor(v.to / 5))
                        + '</span>';
                    if (k % 2 === 1)
                        str += ' ';
                    else
                        str += '<br>';
                }
            });
            $('.moves').html(str);

            var move = 0;

            $('#next').click(function () {
                if (move + 1 === moves.length) return;
                move++;
                drawBoard();
            });

            $('#prev').click(function () {
                if (move === 0) return;
                move--;
                drawBoard();
            });

            function drawBoard() {
                $('.move').removeClass('active');
                $('#move_' + move).addClass('active');
                for (var i = 0; i < 30; i++) {
                    $('#square_' + i).text(moves[move].board.charAt(i));
                }
            }

            drawBoard();

        });
    </script>
@endsection