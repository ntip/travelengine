<div>
    <h5 class="mb-3">
        Provider Config: {{ $provider->code }}
        @if ($provider->name)
            <small class="text-muted">- {{ $provider->name }}</small>
        @endif
    </h5>

    <form wire:submit.prevent="addConfig" class="row g-2 mb-3">
        <div class="col-md-4">
            <input type="text"
                   class="form-control @error('name') is-invalid @enderror"
                   placeholder="Config name (e.g. api_base_url)"
                   wire:model.defer="name">
            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
            <input type="text"
                   class="form-control @error('value') is-invalid @enderror"
                   placeholder="Config value"
                   wire:model.defer="value">
            @error('value') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-2 d-grid">
            <button type="submit" class="btn btn-primary">
                Save
            </button>
        </div>
    </form>

    @if (empty($configs))
        <div class="alert alert-info mb-0">
            No config entries yet. Add your first one above.
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-sm table-striped align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Value</th>
                        <th style="width: 80px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($configs as $cfg)
                        <tr>
                            <td>{{ $cfg['name'] }}</td>
                            <td class="text-break">{{ $cfg['value'] }}</td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-danger"
                                        wire:click="deleteConfig({{ $cfg['id'] }})"
                                        onclick="return confirm('Delete this config?')">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
