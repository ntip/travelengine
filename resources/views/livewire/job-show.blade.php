<div>
    <div class="mb-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Job: {{ $job->route->ori }} → {{ $job->route->dst }} ({{ $job->job_date?->format('Y-m-d') }})</h5>
        <span class="badge bg-secondary">Job ID: {{ $job->id }}</span>
    </div>

    <div class="row g-3">
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-body">
                    <p><strong>Status:</strong> {{ ucfirst($job->status) }}</p>
                    <p><strong>Archived:</strong> {{ $job->archived ? 'Yes' : 'No' }}</p>
                    <p><strong>Next run:</strong> {{ $job->next_run_at?->format('Y-m-d H:i') ?? '—' }}</p>
                    <p><strong>Last hydrated:</strong> {{ $job->last_hydrated_at?->format('Y-m-d H:i') ?? '—' }}</p>
                </div>
            </div>

            <h6>Scrapes</h6>

            @if ($job->scrapes->isEmpty())
                <div class="alert alert-info">No scrapes yet for this job.</div>
            @else
                <div class="accordion" id="scrapesAccordion">
                    @foreach ($job->scrapes as $scrape)
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading{{$scrape->id}}">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{$scrape->id}}">
                                    {{ $scrape->provider_code }} — {{ ucfirst($scrape->status) }} @if($scrape->started_at) ({{ $scrape->started_at->format('Y-m-d H:i') }})@endif
                                </button>
                            </h2>
                            <div id="collapse{{$scrape->id}}" class="accordion-collapse collapse" data-bs-parent="#scrapesAccordion">
                                <div class="accordion-body">
                                    <p><strong>URL:</strong> <code>{{ $scrape->provider_url }}</code></p>
                                    <p><strong>Logs:</strong></p>
                                    @if ($scrape->logs->isEmpty())
                                        <div class="text-muted">No logs recorded.</div>
                                    @else
                                        @foreach ($scrape->logs as $log)
                                            <div class="card mb-2">
                                                <div class="card-body">
                                                    <div class="small text-muted">{{ $log->created_at->format('Y-m-d H:i:s') }}</div>
                                                    <pre class="mt-2">{{ $log->content }}</pre>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <p><strong>Created:</strong> {{ $job->created_at?->format('Y-m-d H:i') }}</p>
                    <p><strong>Updated:</strong> {{ $job->updated_at?->format('Y-m-d H:i') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
