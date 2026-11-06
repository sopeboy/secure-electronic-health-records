@extends('layouts.app')

@section('title', 'Patients')

@section('content')
<div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-3">
    <form method="GET" action="{{ route('patients.index') }}" class="d-flex gap-2">
        <input type="search" name="search" value="{{ $search }}" class="form-control" placeholder="Name or patient number">
        <button class="btn btn-outline-secondary"><i class="bi bi-search"></i></button>
    </form>
    @can('create', App\Models\Patient::class)
        <a href="{{ route('patients.create') }}" class="btn btn-primary"><i class="bi bi-person-plus"></i> Register patient</a>
    @endcan
</div>

@if (auth()->user()->hasRole('doctor', 'nurse'))
    <p class="text-muted small"><i class="bi bi-info-circle"></i> You only see patients assigned to you.</p>
@endif

<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Patient No.</th>
                    <th>Name</th>
                    <th>Gender</th>
                    <th>Age</th>
                    <th>Phone</th>
                    <th class="text-end"></th>
                </tr>
            </thead>
            <tbody>
            @forelse ($patients as $patient)
                <tr>
                    <td class="font-monospace">{{ $patient->patient_number }}</td>
                    <td class="fw-semibold">{{ $patient->full_name }}</td>
                    <td class="text-capitalize">{{ $patient->gender }}</td>
                    <td>{{ $patient->date_of_birth->age }}</td>
                    <td>{{ $patient->phone ?? '—' }}</td>
                    <td class="text-end">
                        <a href="{{ route('patients.show', $patient) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-folder2-open"></i> Open
                        </a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-4">No patients found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">{{ $patients->links() }}</div>
@endsection
