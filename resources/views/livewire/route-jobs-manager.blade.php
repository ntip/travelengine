<div>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">
            Jobs for route:
            <span class="fw-semibold">{{ $route->ori }} → {{ $route->dst }}</span>
        </h5>

        <span class="badge bg-secondary">
            Route UUID: {{ $route->id }}
        </span>
    </div>

    @if ($jobs->isEmpty())
        <div class="alert alert-info mb-0">
            No jobs found for this route yet. Once the scheduler runs with a
            <code>days_ahead</code> config, jobs will appear here.
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-bordered table-sm table-striped align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th scope="col">Job Date</th>
                        <th scope="col">Status</th>
                        <th scope="col">Archived</th>
                        <th scope="col">Next Run At</th>
                        <th scope="col">Last Hydrated At</th>
                        <th scope="col">Created</th>
                        <th scope="col">Updated</th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($jobs as $job)
                        <tr>
                            <td>
                                {{ $job->job_date?->format('Y-m-d') ?? $job->job_date }}
                            </td>

                            <td>
                                @php
                                    $status = $job->status;
                                    $badgeClass = match ($status) {
                                        'pending'  => 'bg-warning text-dark',
                                        'running'  => 'bg-primary',
                                        'success'  => 'bg-success',
                                        'failed'   => 'bg-danger',
                                        'archived' => 'bg-secondary',
                                        default    => 'bg-light text-dark',
                                    };
                                @endphp

                                <span class="badge {{ $badgeClass }}">
                                    {{ ucfirst($status) }}
                                </span>
                            </td>

                            <td>
                                @if ($job->archived)
                                    <span class="badge bg-secondary">Yes</span>
                                @else
                                    <span class="badge bg-success">No</span>
                                @endif
                            </td>

                            <td>
                                {{ $job->next_run_at ? $job->next_run_at->format('Y-m-d H:i') : '—' }}
                            </td>

                            <td>
                                {{ $job->last_hydrated_at ? $job->last_hydrated_at->format('Y-m-d H:i') : '—' }}
                            </td>

                            <td>
                                {{ $job->created_at ? $job->created_at->format('Y-m-d H:i') : '—' }}
                            </td>

                            <td>
                                {{ $job->updated_at ? $job->updated_at->format('Y-m-d H:i') : '—' }}
                            </td>
                            <td>
                                <button
                                    class="btn btn-sm btn-danger"
                                    wire:click="deleteJob({{ $job->id }})"
                                    onclick="return confirm('Are you sure you want to delete this job?')"
                                >
                                    Delete
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $jobs->links() }}
        </div>
    @endif
</div>
