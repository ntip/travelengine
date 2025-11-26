@extends('layouts.admin')

@section('content')
    <div class="container">
        <h2 class="h4">Scrapes</h2>

        <div class="mt-3">
            @livewire('scrapes-manager')
        </div>
    </div>
@endsection
