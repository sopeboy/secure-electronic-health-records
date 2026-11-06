@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="mb-4">
    <h2 class="h4 mb-1">Welcome, {{ $user->name }}</h2>
    <p class="text-muted mb-0">
        Signed in as <strong>{{ $user->role?->label }}</strong>
        @if ($user->last_login_at)
            · last sign-in {{ $user->last_login_at->diffForHumans() }}
        @endif
    </p>
</div>

<div class="row g-3">
    @forelse ($cards as [$label, $value, $icon])
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon"><i class="bi {{ $icon }}"></i></div>
                    <div>
                        <div class="fs-4 fw-bold lh-1">{{ number_format($value) }}</div>
                        <div class="text-muted small">{{ $label }}</div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="alert alert-warning">Your account has no role yet. Contact the administrator.</div>
        </div>
    @endforelse
</div>
@endsection
