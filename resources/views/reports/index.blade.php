@extends('layouts.app')

@section('title', 'Reports')

@section('content')
<p class="text-muted small">Summary figures only. No patient names or clinical details appear in reports.</p>

<div class="row g-3 mb-3">
    @foreach ($totals as $label => $value)
        <div class="col-6 col-lg">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body">
                    <div class="fs-4 fw-bold">{{ number_format($value) }}</div>
                    <div class="text-muted small">{{ $label }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="row g-3">
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white fw-semibold">New patients (last 6 months)</div>
            <table class="table table-sm mb-0">
                @foreach ($months as $month => $count)
                    <tr><td>{{ $month }}</td><td class="text-end fw-semibold">{{ $count }}</td></tr>
                @endforeach
            </table>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white fw-semibold">Patients by gender</div>
            <table class="table table-sm mb-0">
                <tr><td>Male</td><td class="text-end fw-semibold">{{ $byGender['male'] ?? 0 }}</td></tr>
                <tr><td>Female</td><td class="text-end fw-semibold">{{ $byGender['female'] ?? 0 }}</td></tr>
            </table>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white fw-semibold">Staff by role</div>
            <table class="table table-sm mb-0">
                @foreach ($staffByRole as $role)
                    <tr><td>{{ $role->label }}</td><td class="text-end fw-semibold">{{ $role->users_count }}</td></tr>
                @endforeach
            </table>
        </div>
    </div>
</div>
@endsection
