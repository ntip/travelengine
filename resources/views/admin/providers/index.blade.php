@extends('layouts.admin') {{-- or your admin layout --}}

@section('content')
<div class="container my-4">
    <h3 class="mb-3">Providers</h3>

    <livewire:provider-manager />
</div>
@endsection

