<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ProviderGrabberFactory;
use App\Services\Grabbers\VaGrabber;

class DebugVaExtractor extends Command
{
    protected $signature = 'debug:va-extract {file} {provider=VA}';
    protected $description = 'Debug VA extractor: print candidates and chosen item from an Oxylabs result JSON file.';

    public function handle(): int
    {
        $file = $this->argument('file');
        $provider = $this->argument('provider');

        if (! is_file($file)) {
            $this->error("File not found: $file");
            return 1;
        }

        $raw = file_get_contents($file);
        if ($raw === false) {
            $this->error('Unable to read file.');
            return 1;
        }

        $decoded = json_decode($raw, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->warn('File is not valid JSON; trying to treat as raw body content.');
            $decoded = $raw;
        }

        $this->line('--- Running ProviderGrabberFactory::extract ---');
        $extracted = ProviderGrabberFactory::extract($provider, $decoded);
        if ($extracted === null) {
            $this->line('No extracted raw returned by ProviderGrabberFactory.');
        } else {
            $this->line('Extracted raw (first 120 chars):');
            $this->line(substr($extracted, 0, 120));
        }

        $this->line('--- Detailed candidate scan (results -> content) ---');

        if (! is_array($decoded) || ! isset($decoded['results']) || ! is_array($decoded['results'])) {
            $this->warn('No results array found in file.');
            return 0;
        }

        $idx = 0;
        $candidates = [];
        foreach ($decoded['results'] as $res) {
            $idx++;
            $this->info("Result #$idx: url=" . ($res['url'] ?? '(none)') . ' status=' . ($res['status_code'] ?? $res['status'] ?? '(n/a)'));

            if (isset($res['response_body'])) {
                $body = is_string($res['response_body']) ? $res['response_body'] : json_encode($res['response_body']);
                $this->line('  response_body length=' . strlen($body) . ' snippet: ' . substr($body, 0, 80));
            }

            if (! isset($res['content']) || ! is_array($res['content'])) {
                continue;
            }

            $ci = 0;
            foreach ($res['content'] as $c) {
                $ci++;
                $curl = $c['url'] ?? '(none)';
                $method = $c['method'] ?? $c['http_method'] ?? '(n/a)';
                $cstatus = $c['status_code'] ?? $c['status'] ?? '(n/a)';
                $rbody = isset($c['response_body']) && is_string($c['response_body']) ? $c['response_body'] : (isset($c['response_body']) ? json_encode($c['response_body']) : null);

                $this->line("  content #$ci: url=$curl method=$method status=$cstatus");
                if ($rbody !== null) {
                    $this->line('    body len=' . strlen($rbody) . ' snippet: ' . substr($rbody, 0, 120));
                }

                // compute score using same heuristics as VaGrabber (approx)
                $score = 0;
                if (is_numeric($cstatus) && (int)$cstatus === 200) $score += 100;
                if (stripos($curl, 'graphql') !== false || stripos($curl, '/api/') !== false) $score += 50;
                if (stripos($curl, 'metadata.json') !== false || stripos($curl, '/data/') !== false) $score += 20;
                if (is_string($rbody) && strlen($rbody) > 200) $score += 20;
                $low = is_string($rbody) ? strtolower($rbody) : '';
                foreach (['bookingAirSearch','booking','bookings','itinerary','fare','fares','flight','segments','availability','results','data'] as $kw) {
                    if ($low && stripos($low, $kw) !== false) { $score += 40; break; }
                }
                // penalize GraphQL 403 like VaGrabber does
                if (is_numeric($cstatus) && (int)$cstatus === 403 && stripos($curl, 'graphql') !== false) $score -= 1000;

                $candidates[] = ['result_index' => $idx, 'content_index' => $ci, 'url' => $curl, 'method' => $method, 'status' => $cstatus, 'score' => $score, 'body' => $rbody];
            }
        }

        usort($candidates, function ($a, $b) { return $b['score'] <=> $a['score']; });

        $this->line('--- Candidates ranked ---');
        foreach ($candidates as $i => $cand) {
            $this->line(sprintf("#%d R%d.C%d score=%d url=%s status=%s method=%s", $i+1, $cand['result_index'], $cand['content_index'], $cand['score'], $cand['url'], $cand['status'], $cand['method']));
            if (! empty($cand['body'])) {
                $this->line('    snippet: ' . substr($cand['body'], 0, 160));
            }
            if (stripos($cand['body'] ?? '', 'bookingAirSearch') !== false) {
                $this->line('    => contains bookingAirSearch');
            }
        }

        if (count($candidates) > 0) {
            $best = $candidates[0];
            $this->line('--- Best candidate ---');
            $this->line(sprintf('Result %d content %d score=%d url=%s status=%s', $best['result_index'], $best['content_index'], $best['score'], $best['url'], $best['status']));
        }

        $this->line('--- Also running VaGrabber::extract directly ---');
        $vb = new VaGrabber();
        $vout = $vb->extract($decoded);
        if ($vout === null) {
            $this->line('VaGrabber returned null');
        } else {
            $this->line('VaGrabber returned snippet: ' . substr($vout, 0, 240));
        }

        return 0;
    }
}
