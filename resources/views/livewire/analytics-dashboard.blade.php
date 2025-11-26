<div>
    @section('content')
    <div class="container py-4">
        <h1>Scrapes Analytics Dashboard</h1>
        <div class="row mb-4">
            <div class="col-md-4">
                <h4>Success Rate</h4>
                <canvas id="successRateChart"></canvas>
                <ul>
                    @foreach($successRates as $label => $data)
                        <li>{{ $label }}: {{ $data['rate'] }}% ({{ $data['success'] }}/{{ $data['total'] }})</li>
                    @endforeach
                </ul>
            </div>
            <div class="col-md-4">
                <h4>User-Agent Success Rate</h4>
                <table class="table table-sm">
                    <thead><tr><th>User-Agent</th><th>Success</th><th>Total</th><th>Rate</th></tr></thead>
                    <tbody>
                    @foreach($userAgentStats as $ua)
                        <tr>
                            <td>{{ $ua->user_agent }}</td>
                            <td>{{ $ua->success }}</td>
                            <td>{{ $ua->total }}</td>
                            <td>{{ $ua->total ? round($ua->success/$ua->total*100,2) : 0 }}%</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="col-md-4">
                <h4>Top Providers</h4>
                <table class="table table-sm">
                    <thead><tr><th>Provider</th><th>Success</th><th>Total</th><th>Rate</th></tr></thead>
                    <tbody>
                    @foreach($providerStats as $p)
                        <tr>
                            <td>{{ $p->provider }}</td>
                            <td>{{ $p->success }}</td>
                            <td>{{ $p->total }}</td>
                            <td>{{ $p->total ? round($p->success/$p->total*100,2) : 0 }}%</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-md-6">
                <h4>Failure Reasons</h4>
                <table class="table table-sm">
                    <thead><tr><th>Status</th><th>Total</th></tr></thead>
                    <tbody>
                    @foreach($failureReasons as $f)
                        <tr>
                            <td>{{ $f->status }}</td>
                            <td>{{ $f->total }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="col-md-6">
                <h4>Scrape Volume (Top Providers)</h4>
                <table class="table table-sm">
                    <thead><tr><th>Provider</th><th>Total</th></tr></thead>
                    <tbody>
                    @foreach($scrapeVolume as $v)
                        <tr>
                            <td>{{ $v->provider }}</td>
                            <td>{{ $v->total }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-md-12">
                <h4>Recent Scrapes</h4>
                <table class="table table-sm">
                    <thead><tr><th>ID</th><th>Provider</th><th>Status</th><th>Created</th></tr></thead>
                    <tbody>
                    @foreach($recentScrapes as $s)
                        <tr>
                            <td>{{ $s->id }}</td>
                            <td>{{ $s->provider }}</td>
                            <td>{{ $s->status }}</td>
                            <td>{{ $s->created_at }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var ctx = document.getElementById('successRateChart').getContext('2d');
        var chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode(array_keys($successRates)) !!},
                datasets: [{
                    label: 'Success Rate (%)',
                    data: {!! json_encode(array_map(function($d){return $d['rate'];}, $successRates)) !!},
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                }]
            },
            options: {
                scales: {
                    y: { beginAtZero: true, max: 100 }
                }
            }
        });
    });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var ctx = document.getElementById('successRateChart').getContext('2d');
        var chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode(array_keys($successRates)) !!},
                datasets: [{
                    label: 'Success Rate (%)',
                    data: {!! json_encode(array_map(function($d){return $d['rate'];}, $successRates)) !!},
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                }]
            },
            options: {
                scales: {
                    y: { beginAtZero: true, max: 100 }
                }
            }
        });
    });
    </script>

    @endsection
</div>
