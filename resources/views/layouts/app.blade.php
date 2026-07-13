<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Crowdsourcing Hazards Dashboard' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <style>
        body {
            background: #f4f6fb;
            color: #212529;
            min-height: 100vh;
        }
        .sidebar {
            min-height: 100vh;
            background: #0d6efd;
            color: #fff;
            position: relative;
            z-index: 2;
        }
        .sidebar a {
            color: rgba(255,255,255,.92);
            pointer-events: auto;
        }
        .sidebar a.active,
        .sidebar a:hover {
            color: #fff;
            text-decoration: none;
        }
        .card-transparent {
            background: rgba(255,255,255,0.95);
        }
        .table-wrap {
            overflow-x: auto;
        }
        .map-card {
            min-height: 420px;
        }
        .toast-container {
            position: fixed;
            top: 1rem;
            right: 1rem;
            z-index: 1055;
        }
    </style>
    @stack('styles')
</head>
<body>
<div class="d-flex">
    <aside class="sidebar p-4 d-none d-md-block">
        <a href="{{ route('dashboard') }}" class="d-flex align-items-center mb-4 text-white text-decoration-none">
            <i class="bi bi-shield-lock-fill fs-3 me-2"></i>
            <div>
                <div class="fw-bold">Crowdsourcing</div>
                <div class="text-white-75 small">Known Hazards Admin</div>
            </div>
        </a>

        <nav class="nav flex-column gap-2">
            <a class="nav-link px-3 py-2 rounded {{ request()->routeIs('dashboard') ? 'bg-white text-dark' : '' }}" href="{{ route('dashboard') }}"><i class="bi bi-bar-chart-fill me-2"></i> Dashboard</a>
            <a class="nav-link px-3 py-2 rounded {{ request()->routeIs('reports.*') ? 'bg-white text-dark' : '' }}" href="{{ route('reports.index') }}"><i class="bi bi-journal-text me-2"></i> Reports</a>
            <a class="nav-link px-3 py-2 rounded {{ request()->routeIs('map') ? 'bg-white text-dark' : '' }}" href="{{ route('map') }}"><i class="bi bi-geo-alt-fill me-2"></i> Map</a>
            <a class="nav-link px-3 py-2 rounded {{ request()->routeIs('about') ? 'bg-white text-dark' : '' }}" href="{{ route('about') }}"><i class="bi bi-info-circle-fill me-2"></i> About</a>
        </nav>
    </aside>

    <main class="flex-fill">
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm border-bottom px-4 py-3">
            <div class="container-fluid px-0">
                <div class="d-flex align-items-center gap-3">
                    <button class="btn btn-outline-secondary d-md-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarOffcanvas" aria-controls="sidebarOffcanvas">
                        <i class="bi bi-list"></i>
                    </button>
                    <div>
                        <h1 class="h5 mb-0">{{ $pageHeading ?? 'Dashboard' }}</h1>
                        <p class="mb-0 text-muted">{{ $pageDescription ?? 'Manage hazard reports and monitor submission activity.' }}</p>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div class="badge bg-primary rounded-pill py-2 px-3">Production</div>
                    <div class="text-muted small">{{ now()->format('F j, Y') }}</div>
                </div>
            </div>
        </nav>

        <div class="container-fluid px-4 py-4">
            @if(session('success') || session('error'))
                <div class="toast-container">
                    <div class="toast align-items-center text-white {{ session('success') ? 'bg-success' : 'bg-danger' }} border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="d-flex">
                            <div class="toast-body">
                                {{ session('success') ?? session('error') }}
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                    </div>
                </div>
            @endif

            @yield('content')
        </div>
    </main>
</div>

<div class="offcanvas offcanvas-start" tabindex="-1" id="sidebarOffcanvas" aria-labelledby="sidebarOffcanvasLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="sidebarOffcanvasLabel">Admin Menu</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <nav class="nav flex-column gap-2">
            <a class="nav-link" href="{{ route('dashboard') }}" data-bs-dismiss="offcanvas"><i class="bi bi-bar-chart-fill me-2"></i> Dashboard</a>
            <a class="nav-link" href="{{ route('reports.index') }}" data-bs-dismiss="offcanvas"><i class="bi bi-journal-text me-2"></i> Reports</a>
            <a class="nav-link" href="{{ route('map') }}" data-bs-dismiss="offcanvas"><i class="bi bi-geo-alt-fill me-2"></i> Map</a>
            <a class="nav-link" href="{{ route('about') }}" data-bs-dismiss="offcanvas"><i class="bi bi-info-circle-fill me-2"></i> About</a>
        </nav>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-9NDL6mY4M6rDTT+QzMRm32Lq2HPoJ5+PyOGxL7naXKQAOc30C1x2g8vQ8hcq4BzE" crossorigin="anonymous"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var toastElList = [].slice.call(document.querySelectorAll('.toast'));
        toastElList.forEach(function (toastEl) {
            new bootstrap.Toast(toastEl, { delay: 4500 }).show();
        });
    });
</script>
@stack('scripts')
</body>
</html>
