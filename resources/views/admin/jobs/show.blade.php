@extends('layouts.admin')

@section('content')
    <div class="container">
        <h2 class="h4">Job details</h2>

        <div class="mt-3">
            @livewire('job-show', ['job' => $job])
        </div>
    </div>
@endsection
