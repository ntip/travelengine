<div>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">All Scrapes</h5>
    </div>

    @if ($scrapes->isEmpty())
        <div class="alert alert-info">No scrapes available.</div>
    @else
        <div class="table-responsive">
            <table class="table table-bordered table-sm table-striped align-middle">
                <thead>
                    <tr>
                        <th>Provider</th>
                        <th>Route</th>
                        <th>Job Date</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($scrapes as $scrape)
                        <tr>
                            <td>{{ $scrape->provider_code }}</td>
                            <td>{{ optional($scrape->job->route)->ori }} → {{ optional($scrape->job->route)->dst }}</td>
                            <td>{{ optional($scrape->job)->job_date?->format('Y-m-d') ?? '—' }}</td>
                            <td>{{ ucfirst($scrape->status) }}</td>
                            <td>{{ $scrape->created_at?->format('Y-m-d H:i') }}</td>
                            <td>
                                <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.scrapes.show', $scrape) }}">Details</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-3">{{ $scrapes->links() }}</div>
    @endif
</div>
