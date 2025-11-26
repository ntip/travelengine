<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\RouteJob;

class JobsManager extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public function render()
    {
        $jobs = RouteJob::orderBy('job_date')->paginate(25);

        return view('livewire.jobs-manager', [
            'jobs' => $jobs,
        ]);
    }
}
