<?php

namespace App\Livewire;

use App\Models\Route;
use App\Models\RouteConfig;
use Livewire\Component;

class RouteConfigManager extends Component
{
    public Route $route;

    public $configs = [];

    public $name = '';
    public $value = '';

    protected $rules = [
        'name'  => ['required', 'string', 'max:255'],
        'value' => ['nullable', 'string'],
    ];

    public function mount(Route $route): void
    {
        $this->route = $route;
        $this->loadConfigs();
    }

    public function loadConfigs(): void
    {
        $this->configs = $this->route
            ->configs()
            ->orderBy('name')
            ->get()
            ->toArray();
    }

    public function addConfig(): void
    {
        $this->validate();

        RouteConfig::updateOrCreate(
            [
                'route_id' => $this->route->id,
                'name'     => $this->name,
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
        RouteConfig::where('id', $id)
            ->where('route_id', $this->route->id)
            ->delete();

        $this->loadConfigs();
    }

    public function render()
    {
        return view('livewire.route-config-manager');
    }
}
