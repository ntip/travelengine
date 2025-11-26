<?php

namespace App\Livewire;

use App\Models\Provider;
use App\Models\ProviderConfig;
use Livewire\Component;

class ProviderConfigManager extends Component
{
    public Provider $provider;
    public string $name = '';
    public ?string $value = null;
    public array $configs = [];

    protected $rules = [
        'name'  => ['required', 'string', 'max:255'],
        'value' => ['nullable', 'string'],
    ];

    public function mount(Provider $provider): void
    {
        $this->provider = $provider;
        $this->loadConfigs();
    }

    public function loadConfigs(): void
    {
        $this->configs = $this->provider
            ->configs()
            ->orderBy('name')
            ->get()
            ->toArray();
    }

    public function addConfig(): void
    {
        $this->validate();

        ProviderConfig::updateOrCreate(
            [
                'provider_code' => $this->provider->code,
                'name'          => $this->name,
            ],
            [
                'value' => $this->value,
            ]
        );

        $this->reset(['name', 'value']);
        $this->loadConfigs();
    }

    public function deleteConfig(int $id): void
    {
        $config = ProviderConfig::where('id', $id)
            ->where('provider_code', $this->provider->code)
            ->first();

        if ($config) {
            $config->delete();
            $this->loadConfigs();
        }
    }

    public function render()
    {
        return view('livewire.provider-config-manager');
    }
}
