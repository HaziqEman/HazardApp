@extends('layouts.app')

@section('content')
<div class="card shadow-sm rounded-4 border-0 card-transparent p-4">
    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between mb-4 gap-3">
        <div>
            <h3 class="h5 mb-1">Report Management</h3>
            <p class="text-muted mb-0">Search, filter, and review all submitted hazard reports.</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
            <a href="{{ route('map') }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-geo-alt"></i> View Map</a>
            @if(Route::has('reports.export'))
                <a href="{{ route('reports.export', request()->query()) }}" class="btn btn-outline-success btn-sm"><i class="bi bi-file-earmark-spreadsheet"></i> Export CSV</a>
            @endif
            <button type="button" class="btn btn-outline-dark btn-sm" onclick="window.print()"><i class="bi bi-printer"></i> Print</button>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-md-3 g-3 mb-4">
        <div class="col">
            <div class="card rounded-4 border-0 shadow-sm p-3 h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div>
                        <div class="text-muted small">Total Reports</div>
                        <div class="fw-bold fs-4">{{ $reports->total() }}</div>
                    </div>
                    <i class="bi bi-clipboard-data fs-3 text-primary"></i>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card rounded-4 border-0 shadow-sm p-3 h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div>
                        <div class="text-muted small">Displayed</div>
                        <div class="fw-bold fs-4">{{ $reports->count() }}</div>
                    </div>
                    <i class="bi bi-list-columns-reverse fs-3 text-info"></i>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card rounded-4 border-0 shadow-sm p-3 h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div>
                        <div class="text-muted small">Current Category</div>
                        <div class="fw-bold fs-4">{{ request('category') ?: 'All' }}</div>
                    </div>
                    <i class="bi bi-tags fs-3 text-success"></i>
                </div>
            </div>
        </div>
    </div>

    <form class="row g-3 mb-4" method="GET" action="{{ route('reports.index') }}">
        <div class="col-12 col-md-3">
            <input type="search" name="search" class="form-control" placeholder="Search user, description, location" value="{{ request('search') }}">
        </div>
        <div class="col-12 col-md-2">
            <select class="form-select" name="category">
                <option value="">All Categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category }}" {{ request('category') === $category ? 'selected' : '' }}>{{ $category }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-6 col-md-2">
            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}" placeholder="Date from">
        </div>
        <div class="col-6 col-md-2">
            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}" placeholder="Date to">
        </div>
        <div class="col-6 col-md-2">
            <select class="form-select" name="sort">
                <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Newest</option>
                <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Oldest</option>
            </select>
        </div>
        <div class="col-6 col-md-1 d-grid">
            <button type="submit" class="btn btn-primary">Filter</button>
        </div>
    </form>

    <div class="table-wrap">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>User Name</th>
                    <th>Category</th>
                    <th>Description</th>
                    <th>Location</th>
                    <th>Reported Date</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reports as $report)
                    <tr>
                        <td>#{{ $report->id }}</td>
                        <td>{{ $report->user_name }}</td>
                        <td><span class="badge {{ $report->hazard_category === 'Road Hazard' ? 'bg-danger' : ($report->hazard_category === 'Environmental Hazard' ? 'bg-info' : 'bg-success') }}">{{ $report->hazard_category }}</span></td>
                        <td>{{ Str::limit($report->hazard_description, 60) }}</td>
                        <td>{{ $report->location_name ?? '-' }}</td>
                        <td>{{ $report->reported_at->format('Y-m-d H:i') }}</td>
                        <td class="text-end">
                            @if(Route::has('reports.show'))
                                <a href="{{ route('reports.show', $report) }}" class="btn btn-sm btn-outline-primary me-1" title="View report"><i class="bi bi-eye"></i></a>
                            @endif
                            @if(Route::has('reports.destroy'))
                                <form action="{{ route('reports.destroy', $report) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Delete this report?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete report"><i class="bi bi-trash"></i></button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">No hazard reports match your filters.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $reports->links() }}
    </div>
</div>
@endsection
