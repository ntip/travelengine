<?php

namespace App\Console\Commands;

use App\Models\Provider;
use App\Models\Scrape;
use App\Models\ScrapeLog;
use App\Models\RouteJob;
use App\Jobs\RunScrape;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ProcessPendingScrapes extends Command
{
    protected $signature = 'scrapes:process-pending';
    protected $description = 'Process pending route jobs and create scrapes per provider using Oxylabs.';

    public function handle(): int
    {
        $jobs = RouteJob::where('status', 'pending')
            ->where('archived', false)
            ->orderBy('job_date')
            ->get();

        if ($jobs->isEmpty()) {
            $this->info('No pending jobs found.');
            return self::SUCCESS;
        }

        foreach ($jobs as $job) {
            $this->info("Processing job {$job->id} for {$job->job_date}");

            // providers config: comma separated codes on the route
            $providersCsv = $job->route->configValue('providers', '');

            if (empty($providersCsv)) {
                $this->warn("No providers configured for route {$job->route->id}");
                continue;
            }

            $providers = array_filter(array_map('trim', explode(',', $providersCsv)));

            foreach ($providers as $code) {
                $provider = Provider::where('code', $code)->first();

                if (! $provider) {
                    $this->warn("Provider {$code} not found, skipping.");
                    continue;
                }

                $urlTemplate = $provider->configValue('url', null);

                if (! $urlTemplate) {
                    $this->warn("Provider {$code} has no 'url' config, skipping.");
                    continue;
                }

                // Determine date format per-provider (default ISO Y-m-d)
                $dateFormat = $provider->configValue('date_format', 'Y-m-d');

                // Render URL with placeholders; provider can control how {{date}} is formatted
                $url = Str::replace(['{{ori}}', '{{dst}}', '{{date}}'], [
                    $job->route->ori,
                    $job->route->dst,
                    $job->job_date->format($dateFormat),
                ], $urlTemplate);

                // Create a pending scrape record and dispatch a queued job to perform it
                $scrape = Scrape::create([
                    'route_job_id' => $job->id,
                    'provider_code' => $code,
                    'provider_url' => $url,
                    'status' => 'pending',
                    'started_at' => null,
                    'finished_at' => null,
                ]);

                // Dispatch a queued job that will perform the Oxylabs request
                RunScrape::dispatch($scrape);
            }

            // Optionally mark job as running/success etc. For now leave job.status until worker updates.
        }

        return self::SUCCESS;
    }
}
