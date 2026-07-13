@extends('layouts.app')

@section('content')
<div class="card shadow-sm rounded-4 border-0 card-transparent p-4">
    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between mb-4 gap-3">
        <div>
            <h3 class="h5 mb-1">Report Details</h3>
            <p class="text-muted mb-0">Detailed information about the selected hazard report.</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
            <a href="{{ route('map') }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-geo-alt"></i> View Map</a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12 col-lg-7">
            <div class="card p-4 rounded-4 shadow-sm border-0 mb-4">
                <div class="mb-3">
                    <span class="badge {{ $report->hazard_category === 'Road Hazard' ? 'bg-danger' : ($report->hazard_category === 'Environmental Hazard' ? 'bg-info' : 'bg-success') }}">{{ $report->hazard_category }}</span>
                </div>
                <h2 class="h5 mb-3">Reported by {{ $report->user_name }}</h2>
                <p class="text-muted mb-4">{{ $report->hazard_description }}</p>

                <div class="row g-3 mb-4">
                    <div class="col-6">
                        <div class="fw-semibold">Latitude</div>
                        <div>{{ $report->latitude }}</div>
                    </div>
                    <div class="col-6">
                        <div class="fw-semibold">Longitude</div>
                        <div>{{ $report->longitude }}</div>
                    </div>
                    <div class="col-12">
                        <div class="fw-semibold">Location Name</div>
                        <div>{{ $report->location_name ?? 'Not provided' }}</div>
                    </div>
                    <div class="col-12">
                        <div class="fw-semibold">Device Information</div>
                        <div>{{ $report->device_info ?? 'Unknown device' }}</div>
                    </div>
                    <div class="col-12">
                        <div class="fw-semibold">Reported Date</div>
                        <div>{{ $report->reported_at->format('F j, Y H:i') }}</div>
                    </div>
                </div>

                <a href="https://www.google.com/maps/search/?api=1&query={{ $report->latitude }},{{ $report->longitude }}" target="_blank" class="btn btn-primary btn-sm"><i class="bi bi-google"></i> Open in Google Maps</a>
            </div>
        </div>
        <div class="col-12 col-lg-5">
            <div class="card map-card shadow-sm rounded-4 border-0 overflow-hidden">
                <iframe
                    width="100%"
                    height="100%"
                    style="border:0;"
                    loading="lazy"
                    allowfullscreen
                    referrerpolicy="no-referrer-when-downgrade"
                    src="https://www.google.com/maps/embed/v1/place?key={{ config('services.google.maps_api_key') }}&q={{ $report->latitude }},{{ $report->longitude }}&zoom=15">
                </iframe>
            </div>
        </div>
    </div>
</div>
@endsection
