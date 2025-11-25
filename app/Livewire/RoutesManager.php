<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Route as RouteModel; // avoid clash with Route facade

class RoutesManager extends Component
{
    public $ori = '';
    public $dst = '';
    public $showModal = false;

    protected $rules = [
        'ori' => ['required', 'string', 'size:3'],
        'dst' => ['required', 'string', 'size:3', 'different:ori'],
    ];

    public function openModal()
    {
        $this->resetValidation();
        $this->reset(['ori', 'dst']);
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function save()
    {
        $data = $this->validate();

        $ori = strtoupper($data['ori']);
        $dst = strtoupper($data['dst']);

        // Respect unique(ori,dst) constraint; ignore if it already exists
        RouteModel::firstOrCreate([
            'ori' => $ori,
            'dst' => $dst,
        ]);

        $this->showModal = false;

        // Optional small flash or event
        session()->flash('routeSaved', "Route {$ori} → {$dst} saved.");
    }

    public function delete(string $id)
    {
        RouteModel::findOrFail($id)->delete();
    }

    public function render()
    {
        $routes = RouteModel::orderBy('ori')->orderBy('dst')->get();

        return view('livewire.routes-manager', [
            'routes' => $routes,
        ]);
    }
}
