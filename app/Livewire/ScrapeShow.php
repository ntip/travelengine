<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Scrape;

class ScrapeShow extends Component
{
    public Scrape $scrape;

    public function mount(Scrape $scrape): void
    {
        $this->scrape = $scrape;
    }

    public function render()
    {
        $scrape = $this->scrape->load(['job.route', 'logs']);

        return view('livewire.scrape-show', [
            'scrape' => $scrape,
        ]);
    }
}
