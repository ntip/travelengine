<?php

namespace App\Livewire;

use App\Models\Provider;
use Livewire\Component;

class ProviderManager extends Component
{
    public $code = '';
    public $name = '';
    public $country = '';
    public $active = true;
    public $showModal = false;

    protected $rules = [
        'code'    => ['required', 'string', 'min:2', 'max:3'],
        'name'    => ['nullable', 'string', 'max:255'],
        'country' => ['nullable', 'string', 'max:255'],
        'active'  => ['boolean'],
    ];

    public function openModal()
    {
        $this->resetValidation();
        $this->reset(['code', 'name', 'country', 'active']);
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function save()
    {
        $this->validate();

        Provider::create([
            'code'    => strtoupper($this->code),
            'name'    => $this->name,
            'country' => $this->country,
            'active'  => $this->active,
        ]);

        session()->flash('success', "Provider {$this->code} created.");

        $this->closeModal();
    }

    public function delete($code)
    {
        Provider::where('code', $code)->delete();

        session()->flash('success', "Provider $code deleted.");
    }

    public function render()
    {
        $providers = Provider::orderBy('code')->get();

        return view('livewire.provider-manager', compact('providers'));
    }
}
