<?php

namespace App\Jobs;

use App\Models\Scrape;
use App\Models\ScrapeLog;
use App\Services\OxylabsClient;
use App\Services\ProviderGrabberFactory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RunScrape implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Scrape $scrape;

    /**
     * Create a new job instance.
     */
    public function __construct(Scrape $scrape)
    {
        $this->scrape = $scrape;
    }

    /**
     * Execute the job.
     */
    public function handle(OxylabsClient $client): void
    {
        // mark running
        $this->scrape->update(['status' => 'running', 'started_at' => now()]);

        $url = $this->scrape->provider_url;

        // rotate user agent types randomly; providers can override if needed later
        $uaTypes = [
            'desktop','desktop_chrome','desktop_edge','desktop_firefox','desktop_opera','desktop_safari',
            'mobile','mobile_android','mobile_ios',
            'tablet','tablet_android','tablet_ios',
        ];
        $ua = $uaTypes[array_rand($uaTypes)];

        $payload = [
            'source' => 'universal',
            'render' => 'html',
            'url' => $url,
            'xhr' => true,
            'browser_instructions' => [['type' => 'wait', 'wait_time_s' => 4]],
            'user_agent_type' => $ua,
            'context' => [['key' => 'follow_redirects', 'value' => true]],
        ];

        try {
            $result = $client->query($payload);

            $body = $result['body'] ?? null;

            // Save generic log content
            $log = ScrapeLog::create([
                'scrape_id' => $this->scrape->id,
                'route_job_id' => $this->scrape->route_job_id,
                'content' => is_string($body) ? $body : json_encode($body),
                'meta' => [
                    'status' => $result['status'] ?? null,
                    'code' => $result['code'] ?? null,
                ],
            ]);

            // Extract provider-specific raw response using grabber (or generic)
            $raw = ProviderGrabberFactory::extract($this->scrape->provider_code, $body);

            if ($raw) {
                $log->update(['scrape_response_raw' => $raw]);
            }

            // Validate provider response using ProviderResponseValidator
            $validatorResult = \App\Services\ProviderResponseValidator::validate($this->scrape->provider_code, $body, $raw);
            $result = $validatorResult['result'] ?? 'failed';
            $reason = $validatorResult['reason'] ?? null;

            $maxAttempts = config('scrapes.max_attempts', 3);

            if ($result === 'success') {
                $this->scrape->update(['status' => 'success', 'finished_at' => now()]);
            } elseif ($result === 'no-data') {
                $log->update(['meta' => array_merge((array) $log->meta, ['warning' => $reason])]);
                $this->scrape->update(['status' => 'no-data', 'finished_at' => now()]);
                // Create retry if under max attempts
                $attempt = (int) ($this->scrape->attempt ?? 0);
                if ($attempt < $maxAttempts) {
                    $new = \App\Models\Scrape::create([
                        'route_job_id' => $this->scrape->route_job_id,
                        'provider_code' => $this->scrape->provider_code,
                        'provider_url' => $this->scrape->provider_url,
                        'status' => 'pending',
                        'attempt' => $attempt + 1,
                    ]);
                    \App\Jobs\RunScrape::dispatch($new);
                }
            } else {
                // failed or blocked
                $log->update(['meta' => array_merge((array) $log->meta, ['error' => $reason])]);
                $this->scrape->update(['status' => 'failed', 'finished_at' => now()]);

                $attempt = (int) ($this->scrape->attempt ?? 0);
                if ($attempt < $maxAttempts) {
                    $new = \App\Models\Scrape::create([
                        'route_job_id' => $this->scrape->route_job_id,
                        'provider_code' => $this->scrape->provider_code,
                        'provider_url' => $this->scrape->provider_url,
                        'status' => 'pending',
                        'attempt' => $attempt + 1,
                    ]);
                    \App\Jobs\RunScrape::dispatch($new);
                }
            }
        } catch (\Exception $e) {
            ScrapeLog::create([
                'scrape_id' => $this->scrape->id,
                'route_job_id' => $this->scrape->route_job_id,
                'content' => $e->getMessage(),
                'meta' => ['exception' => true],
            ]);

            $this->scrape->update(['status' => 'failed', 'finished_at' => now()]);
        }
    }
}
