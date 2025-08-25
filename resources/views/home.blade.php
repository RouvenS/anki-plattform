@extends('layouts.app')

@section('content')
    <div class="flex justify-center">
        <div class="text-center">
            <h1 class="text-4xl font-bold">Hi {{ $userName }}</h1>
        </div>
    </div>
@endsection