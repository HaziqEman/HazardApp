@extends('layouts.app')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="card shadow-sm rounded-4 border-0 card-transparent p-4">
            <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
                <div>
                    <h3 class="h5 mb-2">About the Hazard Dashboard</h3>
                    <p class="text-muted mb-0">A centralized admin interface for monitoring, reviewing, and managing hazard reports sent from the mobile application.</p>
                </div>
                <div class="text-end">
                    <span class="badge bg-primary">Admin Dashboard</span>
                    <div class="text-muted small mt-1">Responsive Bootstrap 5 interface</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-8">
        <div class="card shadow-sm rounded-4 border-0 card-transparent p-4 h-100">
            <h4 class="mb-3">System Introduction</h4>
            <p class="text-muted">This dashboard is designed to support administrators in analyzing hazard reports collected by a crowdsourced mobile app. It provides real-time reporting, easy filtering, geographic visualization, and an overview of hazard trends across all submissions.</p>

            <h5 class="mt-4 mb-3">Purpose</h5>
            <p class="text-muted">Use this interface to inspect incoming hazard data, track the most active categories, and quickly access location-based reports. The dashboard helps operational teams prioritize response and keep stakeholders informed.</p>

            <div class="row g-3 mt-4">
                <div class="col-12 col-md-6">
                    <div class="card rounded-4 border-0 shadow-sm p-3 h-100">
                        <h6 class="mb-2">Analytics & Insights</h6>
                        <p class="text-muted mb-0">Charts and summary cards provide a clear view of hazard categories and reporting trends over time.</p>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="card rounded-4 border-0 shadow-sm p-3 h-100">
                        <h6 class="mb-2">Report Management</h6>
                        <p class="text-muted mb-0">Filter, search, and review every hazard submission, with the ability to inspect details and remove outdated entries when needed.</p>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="card rounded-4 border-0 shadow-sm p-3 h-100">
                        <h6 class="mb-2">Location Monitoring</h6>
                        <p class="text-muted mb-0">An interactive map displays hazard locations so you can visualize problem areas geographically.</p>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="card rounded-4 border-0 shadow-sm p-3 h-100">
                        <h6 class="mb-2">Fast Review</h6>
                        <p class="text-muted mb-0">Quick access to the latest reports and active user statistics keeps the workflow efficient.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-4">
        <div class="card shadow-sm rounded-4 border-0 card-transparent p-4 h-100">
            <h5 class="mb-3">Technology Stack</h5>
            <ul class="list-unstyled mb-0">
                <li class="mb-3"><span class="badge bg-secondary me-2">Laravel 12</span>PHP backend with Blade templates and MVC routing.</li>
                <li class="mb-3"><span class="badge bg-secondary me-2">Bootstrap 5</span>Responsive frontend UI with utilities and components.</li>
                <li class="mb-3"><span class="badge bg-secondary me-2">MySQL</span>Structured storage for hazard reports and metadata.</li>
                <li class="mb-3"><span class="badge bg-secondary me-2">Google Maps</span>Location visualization and interactive mapping.</li>
                <li><span class="badge bg-secondary me-2">REST API</span>Backend endpoints support mobile app submission workflows.</li>
            </ul>
        </div>

        <div class="card shadow-sm rounded-4 border-0 card-transparent p-4 mt-3">
            <h5 class="mb-3">Feature Overview</h5>
            <div class="d-flex align-items-start gap-2 mb-3">
                <i class="bi bi-check-circle-fill text-success fs-5 mt-1"></i>
                <div>
                    <strong>Dynamic dashboard</strong>
                    <div class="text-muted small">Live summary metrics and charts for fast decision-making.</div>
                </div>
            </div>
            <div class="d-flex align-items-start gap-2 mb-3">
                <i class="bi bi-check-circle-fill text-success fs-5 mt-1"></i>
                <div>
                    <strong>Hazard report list</strong>
                    <div class="text-muted small">Detailed table with search, filters, and actions.</div>
                </div>
            </div>
            <div class="d-flex align-items-start gap-2">
                <i class="bi bi-check-circle-fill text-success fs-5 mt-1"></i>
                <div>
                    <strong>Map monitoring</strong>
                    <div class="text-muted small">See hazard locations across the region in one view.</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
