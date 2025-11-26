<div>
    <div class="mb-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Scrape: {{ $scrape->provider_code }} @if($scrape->job) ({{ $scrape->job->route->ori }} → {{ $scrape->job->route->dst }}) @endif</h5>
        <span class="badge bg-secondary">Scrape ID: {{ $scrape->id }}</span>
    </div>

    <div class="row g-3">
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-body">
                    <p><strong>Status:</strong> {{ ucfirst($scrape->status) }}</p>
                    <p><strong>Provider URL:</strong> <code>{{ $scrape->provider_url }}</code></p>
                    <p><strong>Started:</strong> {{ $scrape->started_at?->format('Y-m-d H:i') ?? '—' }}</p>
                    <p><strong>Finished:</strong> {{ $scrape->finished_at?->format('Y-m-d H:i') ?? '—' }}</p>
                </div>
            </div>

            <h6>Raw provider response</h6>
            @php $raw = $scrape->logs->first()?->scrape_response_raw ?? null; @endphp
            @if ($raw)
                <pre class="card p-3">{{ $raw }}</pre>
            @else
                <div class="alert alert-info">No raw provider response recorded.</div>
            @endif

            <h6 class="mt-3">Logs</h6>
            @if ($scrape->logs->isEmpty())
                <div class="alert alert-info">No logs recorded.</div>
            @else
                @foreach ($scrape->logs as $log)
                    <div class="card mb-2">
                        <div class="card-body">
                            <div class="small text-muted">{{ $log->created_at->format('Y-m-d H:i:s') }}</div>
                            <pre class="mt-2">{{ $log->content }}</pre>
                            @if ($log->request_payload)
                                <div class="mt-2">
                                    <strong>Request payload:</strong>
                                    <pre class="small bg-light p-2">{{ json_encode($log->request_payload, JSON_PRETTY_PRINT) }}</pre>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <p><strong>Created:</strong> {{ $scrape->created_at?->format('Y-m-d H:i') }}</p>
                    <p><strong>Updated:</strong> {{ $scrape->updated_at?->format('Y-m-d H:i') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
