<?php
/** @var App\Game $game */
?>
@extends('layouts.app')

@section('content')
    <table class="chess-diagram table-borderless">
        <tr>
            <td class="board-border">6</td>
            <td colspan="5" rowspan="6">
                <table class="board">
                    @foreach ($game->getBoardCharacters() as $row)
                        <tr>
                            @foreach (str_split($row) as $char)
                                <td>{{ $char }}</td>
                            @endforeach
                        </tr>
                    @endforeach
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
    <script>
        var moves = {!! $game->getMovesJson() !!};

    </script>
@endsection