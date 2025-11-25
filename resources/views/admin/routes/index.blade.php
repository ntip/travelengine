@extends('layouts.admin')

@section('title', 'Home')

@section('content')
<div class="container">
    <h1 class="mt-4">Routes</h1>
    <p>View currently monitored / tracked routes.</p>
    <livewire:routes-manager />
</div>
@endsection
