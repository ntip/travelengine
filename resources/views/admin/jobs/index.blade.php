@extends('layouts.admin')

@section('content')
    <div class="container">
        <h2 class="h4">Jobs</h2>

        <div class="mt-3">
            @livewire('jobs-manager')
        </div>
    </div>
@endsection
