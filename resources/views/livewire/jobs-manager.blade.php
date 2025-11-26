<div>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">All Jobs</h5>
    </div>

    @if ($jobs->isEmpty())
        <div class="alert alert-info">No jobs available.</div>
    @else
        <div class="table-responsive">
            <table class="table table-sm table-striped align-middle">
                <thead>
                    <tr>
                        <th>Route</th>
                        <th>Job Date</th>
                        <th>Status</th>
                        <th>Archived</th>
                        <th>Next Run</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($jobs as $job)
                        <tr>
                            <td>{{ $job->route->ori }} → {{ $job->route->dst }}</td>
                            <td>{{ $job->job_date?->format('Y-m-d') }}</td>
                            <td>{{ ucfirst($job->status) }}</td>
                            <td>{{ $job->archived ? 'Yes' : 'No' }}</td>
                            <td>{{ $job->next_run_at?->format('Y-m-d H:i') ?? '—' }}</td>
                            <td>
                                <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.jobs.show', $job) }}">Details</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-3">{{ $jobs->links() }}</div>
    @endif
</div>
