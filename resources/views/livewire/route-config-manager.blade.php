<div>
    {{-- Add config form --}}
    <form wire:submit.prevent="addConfig" class="mb-3">
        <div class="row g-2">
            <div class="col-md-4">
                <label class="form-label">Name</label>
                <input type="text"
                       class="form-control @error('name') is-invalid @enderror"
                       wire:model="name"
                       placeholder="e.g. max_pax">
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label">Value</label>
                <input type="text"
                       class="form-control @error('value') is-invalid @enderror"
                       wire:model="value"
                       placeholder="e.g. 4">
                @error('value')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    Add / Update
                </button>
            </div>
        </div>
    </form>

    {{-- Config list --}}
    <table class="table table-sm align-middle">
        <thead>
            <tr>
                <th style="width: 40%">Name</th>
                <th style="width: 50%">Value</th>
                <th style="width: 10%"></th>
            </tr>
        </thead>
        <tbody>
        @forelse ($configs as $config)
            <tr>
                <td>{{ $config['name'] }}</td>
                <td>{{ $config['value'] }}</td>
                <td class="text-end">
                    <button type="button"
                            class="btn btn-outline-danger btn-sm"
                            wire:click="deleteConfig({{ $config['id'] }})">
                        Delete
                    </button>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="3" class="text-muted text-center">
                    No config items yet.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>
