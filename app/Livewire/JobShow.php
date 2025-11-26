<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\RouteJob;

class JobShow extends Component
{
    public RouteJob $job;

    public function mount(RouteJob $job): void
    {
        $this->job = $job;
    }

    public function render()
    {
        // eager load scrapes & logs
        $job = $this->job->load(['route', 'scrapes.logs']);

        return view('livewire.job-show', [
            'job' => $job,
        ]);
    }
}
