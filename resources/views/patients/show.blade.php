@extends('layouts.app')

@section('title', $patient->full_name)

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-4">
    <div>
        <h2 class="h4 mb-1">{{ $patient->full_name }}</h2>
        <span class="badge text-bg-light border font-monospace">{{ $patient->patient_number }}</span>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('patients.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Patients</a>
        @can('update', $patient)
            <a href="{{ route('patients.edit', $patient) }}" class="btn btn-outline-primary"><i class="bi bi-pencil"></i> Edit details</a>
        @endcan
    </div>
</div>

<div class="row g-3 mb-3">
    {{-- Demographics --}}
    <div class="col-lg-7">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white fw-semibold"><i class="bi bi-person-vcard"></i> Patient details</div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">Gender</dt>
                    <dd class="col-sm-8 text-capitalize">{{ $patient->gender }}</dd>
                    <dt class="col-sm-4">Date of birth</dt>
                    <dd class="col-sm-8">{{ $patient->date_of_birth->format('d M Y') }} ({{ $patient->date_of_birth->age }} years)</dd>
                    <dt class="col-sm-4">Phone</dt>
                    <dd class="col-sm-8">{{ $patient->phone ?? '—' }}</dd>
                    <dt class="col-sm-4">Address</dt>
                    <dd class="col-sm-8">{{ $patient->address ?? '—' }}</dd>
                    <dt class="col-sm-4">Registered</dt>
                    <dd class="col-sm-8 mb-0">{{ $patient->created_at->format('d M Y') }} by {{ $patient->registeredBy?->name ?? 'system' }}</dd>
                </dl>
            </div>
        </div>
    </div>

    {{-- Care team --}}
    <div class="col-lg-5">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white fw-semibold"><i class="bi bi-people"></i> Care team</div>
            <ul class="list-group list-group-flush">
                @forelse ($patient->assignedStaff as $member)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>{{ $member->name }} <span class="badge text-bg-light border">{{ $member->role?->label }}</span></span>
                        @can('assignStaff', $patient)
                            <form method="POST" action="{{ route('patients.unassign', [$patient, $member]) }}"
                                  data-confirm="Remove {{ $member->name }} from this patient?">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-link text-danger p-0" title="Remove"><i class="bi bi-x-circle"></i></button>
                            </form>
                        @endcan
                    </li>
                @empty
                    <li class="list-group-item text-muted">No doctor or nurse assigned yet.</li>
                @endforelse
            </ul>
            @can('assignStaff', $patient)
                <div class="card-body border-top">
                    @if ($assignable->isEmpty())
                        <p class="text-muted small mb-0">All active doctors and nurses are already assigned.</p>
                    @else
                        <form method="POST" action="{{ route('patients.assign', $patient) }}" class="d-flex gap-2">
                            @csrf
                            <select name="user_id" class="form-select form-select-sm" required>
                                <option value="">Assign doctor or nurse…</option>
                                @foreach ($assignable as $staff)
                                    <option value="{{ $staff->id }}">{{ $staff->name }} ({{ $staff->role->label }})</option>
                                @endforeach
                            </select>
                            <button class="btn btn-sm btn-primary text-nowrap"><i class="bi bi-plus"></i> Assign</button>
                        </form>
                    @endif
                </div>
            @endcan
        </div>
    </div>
</div>

{{-- Clinical sections. Each partial is added by a later part and checks its own permission. --}}
@includeIf('patients.partials.medical-records')
@includeIf('patients.partials.vitals')
@includeIf('patients.partials.prescriptions')
@includeIf('patients.partials.lab-results')

@if (auth()->user()->hasRole('admin'))
    <div class="alert alert-light border small">
        <i class="bi bi-lock"></i> Clinical records are hidden from administrators by design (least privilege).
    </div>
@endif
@endsection

@push('scripts')
<script src="{{ asset('js/sehrs.js') }}"></script>
@endpush
