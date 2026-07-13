@extends('layouts.app')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="row row-cols-1 row-cols-md-4 g-3">
            <div class="col">
                <div class="card shadow-sm rounded-4 border-0 card-transparent p-3">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <span class="badge bg-primary">Total Reports</span>
                        </div>
                        <i class="bi bi-clipboard-data fs-3 text-primary"></i>
                    </div>
                    <h2 class="fw-bold mb-1">{{ number_format($totalReports) }}</h2>
                    <p class="text-muted mb-0">All recorded hazard submissions.</p>
                </div>
            </div>
            <div class="col">
                <div class="card shadow-sm rounded-4 border-0 card-transparent p-3">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <span class="badge bg-danger">Road Hazards</span>
                        </div>
                        <i class="bi bi-speedometer2 fs-3 text-danger"></i>
                    </div>
                    <h2 class="fw-bold mb-1">{{ number_format($roadHazards) }}</h2>
                    <p class="text-muted mb-0">Road hazard reports submitted.</p>
                </div>
            </div>
            <div class="col">
                <div class="card shadow-sm rounded-4 border-0 card-transparent p-3">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <span class="badge bg-info">Environmental</span>
                        </div>
                        <i class="bi bi-tree-fill fs-3 text-info"></i>
                    </div>
                    <h2 class="fw-bold mb-1">{{ number_format($environmentalHazards) }}</h2>
                    <p class="text-muted mb-0">Environment hazard submissions.</p>
                </div>
            </div>
            <div class="col">
                <div class="card shadow-sm rounded-4 border-0 card-transparent p-3">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <span class="badge bg-success">Building Hazards</span>
                        </div>
                        <i class="bi bi-building fs-3 text-success"></i>
                    </div>
                    <h2 class="fw-bold mb-1">{{ number_format($buildingHazards) }}</h2>
                    <p class="text-muted mb-0">Building hazard reports submitted.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card shadow-sm rounded-4 border-0 card-transparent p-4">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h3 class="h5 mb-1">Latest Reports</h3>
                    <p class="text-muted mb-0">Live feed of the most recent hazard submissions.</p>
                </div>
                <a href="{{ route('reports.index') }}" class="btn btn-outline-primary btn-sm">Manage Reports</a>
            </div>
            <div class="table-wrap">
                <table class="table table-borderless align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Category</th>
                            <th>Description</th>
                            <th>Location</th>
                            <th>Reported</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($latestReports as $report)
                            <tr class="align-top border-bottom">
                                <td>#{{ $report->id }}</td>
                                <td>{{ $report->user_name }}</td>
                                <td><span class="badge {{ $report->hazard_category === 'Road Hazard' ? 'bg-danger' : ($report->hazard_category === 'Environmental Hazard' ? 'bg-info' : 'bg-success') }}">{{ $report->hazard_category }}</span></td>
                                <td>{{ Str::limit($report->hazard_description, 70) }}</td>
                                <td>{{ $report->location_name ?? 'Unknown' }}</td>
                                <td>{{ $report->reported_at->format('d M Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">No hazard reports available yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-6">
        <div class="card shadow-sm rounded-4 border-0 card-transparent p-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <h3 class="h6 mb-0">Category Distribution</h3>
                    <p class="text-muted small mb-0">Hazard reports broken down by category.</p>
                </div>
            </div>
            <canvas id="categoryPieChart" height="250"></canvas>
        </div>
    </div>

    <div class="col-12 col-xl-6">
        <div class="card shadow-sm rounded-4 border-0 card-transparent p-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <h3 class="h6 mb-0">Reports per Day</h3>
                    <p class="text-muted small mb-0">Submission trend over the last 14 days.</p>
                </div>
            </div>
            <canvas id="reportsLineChart" height="250"></canvas>
        </div>
    </div>

    <div class="col-12">
        <div class="card shadow-sm rounded-4 border-0 card-transparent p-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <h3 class="h6 mb-0">Reports by Category</h3>
                    <p class="text-muted small mb-0">Aggregated totals for each hazard type.</p>
                </div>
            </div>
            <canvas id="categoryBarChart" height="170"></canvas>
        </div>
    </div>

    <div class="col-12 col-xl-4">
        <div class="card shadow-sm rounded-4 border-0 card-transparent p-4">
            <h3 class="h6 mb-3">Top Hazard Category</h3>
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <p class="mb-2 text-muted">Most reported hazard type</p>
                    <h4 class="fw-bold">{{ $categoryDistribution->sortDesc()->keys()->first() ?? 'N/A' }}</h4>
                </div>
                <div class="text-primary fs-1"><i class="bi bi-tags-fill"></i></div>
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-4">
        <div class="card shadow-sm rounded-4 border-0 card-transparent p-4">
            <h3 class="h6 mb-3">Latest Submission</h3>
            <p class="text-muted mb-2">Newest report from the Android app.</p>
            <div class="fw-bold">{{ $latestSubmission?->user_name ?? 'N/A' }}</div>
            <div class="text-muted">{{ $latestSubmission?->hazard_category ?? 'No reports' }}</div>
            <div class="text-muted small">{{ $latestSubmission?->reported_at?->format('d M Y H:i') ?? '' }}</div>
        </div>
    </div>

    <div class="col-12 col-xl-4">
        <div class="card shadow-sm rounded-4 border-0 card-transparent p-4">
            <h3 class="h6 mb-3">Most Active User</h3>
            <p class="text-muted mb-2">Top contributor of hazard reports.</p>
            <div class="fw-bold">{{ $mostActiveUser?->user_name ?? 'N/A' }}</div>
            <div class="text-muted">{{ $mostActiveUser?->reports ? $mostActiveUser->reports . ' reports' : 'No data' }}</div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    const categoryLabels = @json(array_keys($categoryDistribution->toArray()));
    const categoryValues = @json(array_values($categoryDistribution->toArray()));

    new Chart(document.getElementById('categoryPieChart'), {
        type: 'pie',
        data: {
            labels: categoryLabels,
            datasets: [{
                data: categoryValues,
                backgroundColor: ['#dc3545', '#0dcaf0', '#198754'],
                borderWidth: 1,
            }]
        },
        options: {
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });

    new Chart(document.getElementById('reportsLineChart'), {
        type: 'line',
        data: {
            labels: @json($reportsPerDay->keys()->toArray()),
            datasets: [{
                label: 'Reports',
                data: @json($reportsPerDay->values()->toArray()),
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.15)',
                fill: true,
                tension: 0.3,
                pointRadius: 4,
            }]
        },
        options: {
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    new Chart(document.getElementById('categoryBarChart'), {
        type: 'bar',
        data: {
            labels: categoryLabels,
            datasets: [{
                label: 'Hazard Reports',
                data: categoryValues,
                backgroundColor: ['#dc3545', '#0dcaf0', '#198754'],
            }]
        },
        options: {
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>
@endpush
