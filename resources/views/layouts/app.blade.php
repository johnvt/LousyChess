<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

    <!-- Styles -->
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
            font-family: Chess;
            src: url('{{ public_path('fonts/chess.tff') }}');
        }
        .board .tile {
            font-family: Chess;
        }
    </style>
</head>
<body>
<div class="container">
    @yield('content')
</div>
</body>
</html>
