<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') · SEHRS</title>
    {{-- Assets are served locally (no CDN) so the Content-Security-Policy can be 'self' only --}}
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sehrs.css') }}">
</head>
<body>
@php
    $user = auth()->user();
    // Menu items only appear once the part that creates the route has been merged.
    $menu = [
        ['route' => 'dashboard',        'icon' => 'bi-speedometer2',      'label' => 'Dashboard',  'roles' => ['admin', 'doctor', 'nurse', 'lab']],
        ['route' => 'patients.index',   'icon' => 'bi-people',            'label' => 'Patients',   'roles' => ['admin', 'doctor', 'nurse', 'lab']],
        ['route' => 'staff.index',      'icon' => 'bi-person-badge',      'label' => 'Staff',      'roles' => ['admin']],
        ['route' => 'reports.index',    'icon' => 'bi-bar-chart',         'label' => 'Reports',    'roles' => ['admin', 'doctor']],
        ['route' => 'audit-logs.index', 'icon' => 'bi-shield-lock',       'label' => 'Audit Logs', 'roles' => ['admin']],
    ];
@endphp

{{-- Mobile top bar --}}
<nav class="navbar bg-sehrs navbar-dark d-lg-none px-3">
    <span class="navbar-brand fw-bold"><i class="bi bi-shield-plus"></i> SEHRS</span>
    <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebar" aria-controls="sidebar" aria-label="Open menu">
        <span class="navbar-toggler-icon"></span>
    </button>
</nav>

<div class="d-lg-flex">
    {{-- Sidebar --}}
    <aside class="sidebar offcanvas-lg offcanvas-start" tabindex="-1" id="sidebar" aria-label="Main menu">
        <div class="offcanvas-header d-lg-none">
            <span class="brand">SEHRS</span>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" data-bs-target="#sidebar" aria-label="Close"></button>
        </div>
        <div class="d-flex flex-column h-100 p-3">
            <div class="brand fs-5 mb-4 d-none d-lg-block"><i class="bi bi-shield-plus"></i> SEHRS</div>
            <ul class="nav flex-column gap-1">
                @foreach ($menu as $item)
                    @if (Route::has($item['route']) && $user?->hasRole(...$item['roles']))
                        <li class="nav-item">
                            <a href="{{ route($item['route']) }}"
                               class="nav-link {{ request()->routeIs(Str::before($item['route'], '.index') . '*') ? 'active' : '' }}">
                                <i class="bi {{ $item['icon'] }}"></i> {{ $item['label'] }}
                            </a>
                        </li>
                    @endif
                @endforeach
            </ul>
            <div class="mt-auto small opacity-75 pt-4">
                Academic prototype<br>Synthetic data only
            </div>
        </div>
    </aside>

    {{-- Main content --}}
    <div class="main flex-grow-1">
        <header class="topbar d-flex align-items-center justify-content-between px-4 py-3">
            <h1 class="h5 mb-0">@yield('title', 'Dashboard')</h1>
            @if ($user)
                <div class="d-flex align-items-center gap-3">
                    <div class="text-end lh-sm d-none d-sm-block">
                        <div class="fw-semibold">{{ $user->name }}</div>
                        <span class="badge text-bg-light border">{{ $user->role?->label ?? 'No role' }}</span>
                    </div>
                    @if (Route::has('logout'))
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-box-arrow-right"></i> Sign out
                            </button>
                        </form>
                    @endif
                </div>
            @endif
        </header>

        <main class="p-4">
            @if (session('status'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('status') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

<script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
@stack('scripts')
</body>
</html>
