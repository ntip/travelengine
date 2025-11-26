@extends('layouts.admin')

@section('title', 'Home')

@section('content')
    <div class="container py-4">
        <div class="mb-3">
            <a href="{{ route('admin.routes') }}" class="btn btn-sm btn-outline-secondary">
                ← Back to Routes
            </a>
        </div>

        <div class="card">
            <div class="card-header">
                Route Details
            </div>
            <div class="card-body">
                <h5 class="card-title">
                    {{ $route->ori }} → {{ $route->dst }}
                </h5>

                <p class="card-text mb-1">
                    <strong>ID:</strong> {{ $route->id }}
                </p>
                <p class="card-text mb-1">
                    <strong>Origin:</strong> {{ $route->ori }}
                </p>
                <p class="card-text mb-1">
                    <strong>Destination:</strong> {{ $route->dst }}
                </p>
                <p class="card-text mb-0">
                    <strong>Created:</strong>
                    {{ $route->created_at?->format('Y-m-d H:i') }}
                </p>
            </div>
        </div>
    </div>
    <div class="container py-4">
        <div class="card">
            <div class="card-header">
                Route Config
            </div>
            <div class="card-body">
                @livewire('route-config-manager', ['route' => $route])
            </div>
        </div>
    </div>
    <div class="container py-4">
        <div class="card">
            <div class="card-header">
                Jobs Details
            </div>
            <div class="card-body">
                <livewire:route-jobs-manager :route="$route" />
            </div>
        </div>
    </div>


@endsection
