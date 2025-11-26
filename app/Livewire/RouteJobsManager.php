<?php

namespace App\Livewire;

use App\Models\Route;
use App\Models\RouteJob;
use Livewire\Component;
use Livewire\WithPagination;

class RouteJobsManager extends Component
{
    use WithPagination;

    public Route $route;

    // Optional: keep pagination query string clean per-route
    protected $paginationTheme = 'bootstrap';

    public function mount(Route $route): void
    {
        // Route is injected with the UUID from the URL / parent
        $this->route = $route;
    }

    public function render()
    {
        $jobs = RouteJob::where('route_id', $this->route->id)
            ->orderBy('job_date')
            ->orderBy('status')
            ->paginate(20);

        return view('livewire.route-jobs-manager', [
            'jobs' => $jobs,
        ]);
    }

    public function deleteJob(int $id): void
    {
        $job = RouteJob::where('route_id', $this->route->id)
            ->where('id', $id)
            ->first();

        if (! $job) {
            session()->flash('error', 'Job not found.');
            return;
        }

        $job->delete();

        session()->flash('success', 'Job deleted.');

        // Refresh pagination if needed
        $this->resetPage();
    }

}
