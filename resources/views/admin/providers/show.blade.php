@extends('layouts.admin') {{-- or admin layout --}}

@section('content')
<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">
            Provider: {{ $provider->code }}
            @if ($provider->name)
                <small class="text-muted">- {{ $provider->name }}</small>
            @endif
        </h3>

        <a href="{{ route('admin.providers') }}" class="btn btn-sm btn-outline-secondary">
            ← Back to list
        </a>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header">
                    Details
                </div>
                <div class="card-body">
                    <p><strong>Code:</strong> {{ $provider->code }}</p>
                    <p><strong>Name:</strong> {{ $provider->name ?? '—' }}</p>
                    <p><strong>Country:</strong> {{ $provider->country ?? '—' }}</p>
                    <p>
                        <strong>Status:</strong>
                        @if ($provider->active)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </p>
                    <p><strong>Created:</strong> {{ $provider->created_at ?? '—' }}</p>
                    <p><strong>Updated:</strong> {{ $provider->updated_at ?? '—' }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-8 mb-4">
            {{-- Config manager Livewire component --}}
            <livewire:provider-config-manager :provider="$provider" />
        </div>
    </div>
</div>
@endsection
