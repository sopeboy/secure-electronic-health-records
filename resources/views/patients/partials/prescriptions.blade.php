@can('viewClinical', $patient)
<div class="card shadow-sm border-0 mb-3" id="prescriptions">
    <div class="card-header bg-white fw-semibold"><i class="bi bi-capsule"></i> Prescriptions</div>
    <div class="card-body">
        @can('prescribe', $patient)
            <form method="POST" action="{{ route('patients.prescriptions.store', $patient) }}" class="mb-3">
                @csrf
                <div class="row g-2">
                    <div class="col-md-3">
                        <input name="medication" value="{{ old('medication') }}" placeholder="Medication" required maxlength="255"
                               class="form-control @error('medication', 'prescriptions') is-invalid @enderror">
                        @error('medication', 'prescriptions')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <input name="dosage" value="{{ old('dosage') }}" placeholder="Dosage e.g. 500mg twice daily" required maxlength="255"
                               class="form-control @error('dosage', 'prescriptions') is-invalid @enderror">
                        @error('dosage', 'prescriptions')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <input name="instructions" value="{{ old('instructions') }}" placeholder="Instructions (optional)" maxlength="500"
                               class="form-control @error('instructions', 'prescriptions') is-invalid @enderror">
                        @error('instructions', 'prescriptions')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-2 d-grid">
                        <button class="btn btn-primary"><i class="bi bi-plus"></i> Add</button>
                    </div>
                </div>
            </form>
        @endcan

        @php $prescriptions = $patient->prescriptions()->with('prescriber')->get(); @endphp
        @if ($prescriptions->isEmpty())
            <p class="text-muted mb-0">No prescriptions yet.</p>
        @else
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead><tr><th>Date</th><th>Medication</th><th>Dosage</th><th>Instructions</th><th>Doctor</th></tr></thead>
                    <tbody>
                    @foreach ($prescriptions as $rx)
                        <tr>
                            <td class="text-nowrap">{{ $rx->created_at->format('d M Y') }}</td>
                            <td class="fw-semibold">{{ $rx->medication }}</td>
                            <td>{{ $rx->dosage }}</td>
                            <td>{{ $rx->instructions ?? '—' }}</td>
                            <td>{{ $rx->prescriber?->name ?? '—' }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endcan
