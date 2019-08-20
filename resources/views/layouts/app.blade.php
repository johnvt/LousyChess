<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
          integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <style>
        html, body {
            background-color: #fff;
            color: #636b6f;
            font-family: 'Nunito', sans-serif;
            font-weight: 200;
            height: 100vh;
            margin: 0;
        }

        @font-face {
            font-family: 'Chess Regular';
            font-style: normal;
            font-weight: normal;
            src: local('Chess Regular'), url('{{ asset('fonts/CHEQ_TT.woff') }}') format('woff');
        }

        .board .tile {
            font-family: "Chess Regular";
        }

        .chess-diagram {
            border: none;
            margin: 0;
        }

        .chess-diagram td {
            border: none;
            padding: 0;
            margin: 0;
        }

        .chess-diagram .board-border {
            line-height: 1rem;
            font-size: 1rem;
            text-align: center;
            vertical-align: center;
            padding: .3rem;
        }

        .chess-diagram .board {
            color: black;
            border: 1px solid gray;
            margin: 0;
            font-family: 'Chess Regular', monospace;
            line-height: 5rem;
            font-size: 5rem;
        }
    </style>
</head>
<body>
<div class="container">
    <ul class="nav">
        <li class="nav-item">
            <a class="nav-link" href="{{ route('games.index') }}">Games</a>
        </li>
    </ul>
    @yield('content')
</div>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
        crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
        integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1"
        crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
        crossorigin="anonymous"></script>
</body>
</html>
