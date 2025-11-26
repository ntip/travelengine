@extends('layouts.admin')

@section('content')
    <div class="container">
        <h2 class="h4">Scrape details</h2>

        <div class="mt-3">
            @livewire('scrape-show', ['scrape' => $scrape])
        </div>
    </div>
@endsection
