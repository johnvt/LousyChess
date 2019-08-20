@extends('layouts.app')

@section('content')
    <div class="board">
        @foreach ($tiles as $tile)
            <span class="tile">
                {{ $tile }}
            </span>
        @endforeach
    </div>
@endsection