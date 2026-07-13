@extends('layouts.app')

@section('content')
@php
    $categoryCounts = $hazards->groupBy('hazard_category')->map->count();
@endphp
<div class="card shadow-sm rounded-4 border-0 card-transparent p-4">
    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between mb-4 gap-3">
        <div>
            <h3 class="h5 mb-1">Hazard Monitoring Map</h3>
            <p class="text-muted mb-0">Visualize hazard submissions from the field with category markers and location detail.</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
            <a href="{{ route('reports.index') }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-journal-text"></i> Manage Reports</a>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-md-3 g-3 mb-4">
        <div class="col">
            <div class="card rounded-4 border-0 shadow-sm p-3 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small">Total Hazards</div>
                        <div class="fw-bold fs-4">{{ $hazards->count() }}</div>
                    </div>
                    <i class="bi bi-pin-map fs-3 text-primary"></i>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card rounded-4 border-0 shadow-sm p-3 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small">Road Hazards</div>
                        <div class="fw-bold fs-4">{{ $categoryCounts->get('Road Hazard', 0) }}</div>
                    </div>
                    <i class="bi bi-truck fs-3 text-danger"></i>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card rounded-4 border-0 shadow-sm p-3 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small">Environmental / Building</div>
                        <div class="fw-bold fs-4">{{ $categoryCounts->get('Environmental Hazard', 0) + $categoryCounts->get('Building Hazard', 0) }}</div>
                    </div>
                    <i class="bi bi-tree-fill fs-3 text-success"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="card map-card rounded-4 border-0 overflow-hidden shadow-sm">
        <div id="hazardMap" style="width:100%; height:100%; min-height:520px;"></div>
        @if($hazards->isEmpty())
            <div class="p-4 text-center text-muted">
                <p class="mb-0">No hazard location data available yet. Check back after reports are submitted.</p>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
@php
    $googleMapsApiKey = config('services.google.maps_api_key') ?: env('GOOGLE_MAPS_API_KEY');
@endphp
@if($googleMapsApiKey)
<script async defer src="https://maps.googleapis.com/maps/api/js?key={{ $googleMapsApiKey }}&callback=initHazardMap"></script>
<script>
    function mapIcon(category) {
        return {
            url: category === 'Road Hazard' ? 'https://maps.google.com/mapfiles/ms/icons/red-dot.png' : category === 'Environmental Hazard' ? 'https://maps.google.com/mapfiles/ms/icons/blue-dot.png' : 'https://maps.google.com/mapfiles/ms/icons/green-dot.png',
            scaledSize: new google.maps.Size(32, 32),
        };
    }

    function initHazardMap() {
        const map = new google.maps.Map(document.getElementById('hazardMap'), {
            center: { lat: 6.452312, lng: 100.278912 },
            zoom: 6,
        });

        const hazards = @json($hazards);
        const bounds = new google.maps.LatLngBounds();

        hazards.forEach(hazard => {
            if (!hazard.latitude || !hazard.longitude) {
                return;
            }

            const marker = new google.maps.Marker({
                position: { lat: parseFloat(hazard.latitude), lng: parseFloat(hazard.longitude) },
                map,
                icon: mapIcon(hazard.hazard_category),
                title: hazard.hazard_category || 'Hazard',
            });

            bounds.extend(marker.position);

            const reportedAt = hazard.reported_at ? new Date(hazard.reported_at).toLocaleString() : 'Unknown';
            const description = hazard.hazard_description ? hazard.hazard_description.substring(0, 120) : 'No description provided.';

            const infoWindow = new google.maps.InfoWindow({
                content: `
                    <div style="max-width: 280px; font-family: Arial, sans-serif;">
                        <h6 class="mb-1">${hazard.user_name || 'Unknown reporter'}</h6>
                        <p class="mb-1"><strong>Category:</strong> ${hazard.hazard_category || 'Unknown'}</p>
                        <p class="mb-2" style="margin:0;">${description}${hazard.hazard_description && hazard.hazard_description.length > 120 ? '...' : ''}</p>
                        <p class="mb-0 text-muted"><small>Reported at ${reportedAt}</small></p>
                    </div>
                `,
            });

            marker.addListener('click', () => {
                infoWindow.open(map, marker);
            });
        });

        if (hazards.length) {
            map.fitBounds(bounds, 120);
        }
    }

    document.addEventListener('DOMContentLoaded', initHazardMap);
</script>
@else
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('hazardMap');
        if (container) {
            container.innerHTML = '<div class="d-flex align-items-center justify-content-center h-100 text-muted">Google Maps API key not configured. Map cannot be loaded.</div>';
        }
    });
</script>
@endif
@endpush
