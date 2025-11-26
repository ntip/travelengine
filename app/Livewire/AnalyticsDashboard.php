<?php
namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsDashboard extends Component
{
    public $successRates;
    public $userAgentStats;
    public $providerStats;
    public $failureReasons;
    public $recentScrapes;
    public $scrapeVolume;

    public function mount()
    {
        // Time windows
        $now = Carbon::now();
        $windows = [
            '2h' => $now->copy()->subHours(2),
            '24h' => $now->copy()->subHours(24),
            'month' => $now->copy()->subDays(30),
        ];

        // Success rates
        $this->successRates = [];
        foreach ($windows as $label => $start) {
            $total = DB::table('scrapes')->where('created_at', '>=', $start)->count();
            $success = DB::table('scrape_logs')
                ->where('created_at', '>=', $start)
                ->where('status', 'success')
                ->count();
            $rate = $total ? round($success / $total * 100, 2) : 0;
            $this->successRates[$label] = [
                'total' => $total,
                'success' => $success,
                'rate' => $rate,
            ];
        }

        // User-agent success rates
        $this->userAgentStats = DB::table('scrape_logs')
            ->select(DB::raw("json_extract(request_payload, '$.headers.User-Agent') as user_agent"),
                DB::raw('count(*) as total'),
                DB::raw("sum(case when status = 'success' then 1 else 0 end) as success"))
            ->groupBy('user_agent')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        // Top providers by success rate (join with scrapes for provider_code)
        $this->providerStats = DB::table('scrape_logs')
            ->join('scrapes', 'scrape_logs.scrape_id', '=', 'scrapes.id')
            ->select('scrapes.provider_code as provider',
                DB::raw('count(*) as total'),
                DB::raw("sum(case when scrape_logs.status = 'success' then 1 else 0 end) as success"))
            ->groupBy('scrapes.provider_code')
            ->orderByDesc('success')
            ->limit(10)
            ->get();

        // Failure reasons
        $this->failureReasons = DB::table('scrape_logs')
            ->select('status', DB::raw('count(*) as total'))
            ->where('status', '!=', 'success')
            ->groupBy('status')
            ->orderByDesc('total')
            ->get();

        // Recent activity (join with scrapes for provider_code)
        $this->recentScrapes = DB::table('scrape_logs')
            ->join('scrapes', 'scrape_logs.scrape_id', '=', 'scrapes.id')
            ->select('scrape_logs.id', 'scrapes.provider_code as provider', 'scrape_logs.status', 'scrape_logs.created_at')
            ->orderByDesc('scrape_logs.created_at')
            ->limit(10)
            ->get();

        // Scrape volume per provider (use provider_code)
        $this->scrapeVolume = DB::table('scrapes')
            ->select('provider_code as provider', DB::raw('count(*) as total'))
            ->groupBy('provider_code')
            ->orderByDesc('total')
            ->limit(10)
            ->get();
    }

    public function render()
    {
        return view('livewire.analytics-dashboard')
            ->layout('layouts.admin');
    }
}
