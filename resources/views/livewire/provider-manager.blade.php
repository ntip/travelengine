<div>

    <!-- Button -->
    <button class="btn btn-primary mb-3" wire:click="openModal">
        Add Provider
    </button>

    <!-- Modal -->
    @if ($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background:rgba(0,0,0,0.5);">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form wire:submit.prevent="save">
                        <div class="modal-header">
                            <h5 class="modal-title">Create Provider</h5>
                            <button type="button" class="btn-close" wire:click="closeModal"></button>
                        </div>

                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Code (VA, EY, CX)</label>
                                <input type="text" class="form-control" wire:model.defer="code">
                                @error('code') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Name</label>
                                <input type="text" class="form-control" wire:model.defer="name">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Country</label>
                                <input type="text" class="form-control" wire:model.defer="country">
                            </div>

                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" wire:model="active" id="activeCheck">
                                <label for="activeCheck" class="form-check-label">Active</label>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" wire:click="closeModal">Close</button>
                            <button type="submit" class="btn btn-primary">Save Provider</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- List of Providers -->
    <table class="table table-striped align-middle">
        <thead>
            <tr>
                <th>Code</th>
                <th>Name</th>
                <th>Country</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($providers as $provider)
                <tr>
                    <td>{{ $provider->code }}</td>
                    <td>{{ $provider->name ?? '—' }}</td>
                    <td>{{ $provider->country ?? '—' }}</td>
                    <td>
                        @if ($provider->active)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <a href="{{ route('admin.providers.show', $provider->code) }}"
                        class="btn btn-sm btn-outline-primary me-1">
                            Details
                        </a>

                        <button class="btn btn-sm btn-outline-danger"
                                wire:click="delete('{{ $provider->code }}')"
                                onclick="return confirm('Delete provider {{ $provider->code }}?')">
                            Delete
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</div>
