<div>
    {{-- Header + Add button --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h4 mb-0">Routes</h2>

        <button type="button"
                class="btn btn-primary"
                wire:click="openModal">
            Add Route
        </button>
    </div>

    {{-- Flash message --}}
    @if (session('routeSaved'))
        <div class="alert alert-success py-2">
            {{ session('routeSaved') }}
        </div>
    @endif

    {{-- Routes table --}}
    <div class="table table-responsive table-bordered">
        <table class="table table-sm table-striped align-middle">
            <thead>
                <tr>
                    <th scope="col">Origin</th>
                    <th scope="col">Destination</th>
                    <th scope="col">Created</th>
                    <th scope="col" class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($routes as $route)
                    <tr>
                        <td>{{ $route->ori }}</td>
                        <td>{{ $route->dst }}</td>
                        <td>{{ $route->created_at?->format('Y-m-d H:i') }}</td>
                        <td class="text-end">
                            <a href="{{ route('admin.routes.show', $route) }}"
                            class="btn btn-sm btn-outline-primary me-1">
                                Details
                            </a>

                            <button type="button"
                                    class="btn btn-sm btn-outline-danger"
                                    wire:click="delete('{{ $route->id }}')"
                                    onclick="return confirm('Delete this route?')">
                                Delete
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted">
                            No routes yet. Click <strong>Add Route</strong> to create one.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Modal overlay --}}
    @if ($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.5);">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form wire:submit.prevent="save">
                        <div class="modal-header">
                            <h5 class="modal-title">Add Route</h5>
                            <button type="button"
                                    class="btn-close"
                                    aria-label="Close"
                                    wire:click="closeModal"></button>
                        </div>

                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Origin (IATA)</label>
                                <input type="text"
                                       class="form-control @error('ori') is-invalid @enderror"
                                       wire:model.defer="ori"
                                       maxlength="3"
                                       style="text-transform: uppercase;">
                                @error('ori')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Destination (IATA)</label>
                                <input type="text"
                                       class="form-control @error('dst') is-invalid @enderror"
                                       wire:model.defer="dst"
                                       maxlength="3"
                                       style="text-transform: uppercase;">
                                @error('dst')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button"
                                    class="btn btn-outline-secondary"
                                    wire:click="closeModal">
                                Cancel
                            </button>
                            <button type="submit" class="btn btn-primary">
                                Save Route
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Prevent body scroll when modal is open --}}
        <style>
            body { overflow: hidden; }
        </style>
    @endif
</div>
