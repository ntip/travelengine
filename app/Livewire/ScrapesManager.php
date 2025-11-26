<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Scrape;

class ScrapesManager extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public function render()
    {
        $scrapes = Scrape::with('job.route')
            ->orderBy('created_at', 'desc')
            ->paginate(25);

        return view('livewire.scrapes-manager', [
            'scrapes' => $scrapes,
        ]);
    }
}
