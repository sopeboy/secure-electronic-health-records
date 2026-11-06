@can('viewClinical', $patient)
<div class="card shadow-sm border-0 mb-3" id="records">
    <div class="card-header bg-white fw-semibold"><i class="bi bi-journal-medical"></i> Diagnoses</div>
    <div class="card-body">
        @can('diagnose', $patient)
            <form method="POST" action="{{ route('patients.records.store', $patient) }}" class="mb-3">
                @csrf
                <div class="row g-2">
                    <div class="col-md-5">
                        <input name="diagnosis" value="{{ old('diagnosis') }}" placeholder="Diagnosis" required maxlength="2000"
                               class="form-control @error('diagnosis', 'records') is-invalid @enderror">
                        @error('diagnosis', 'records')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-5">
                        <input name="notes" value="{{ old('notes') }}" placeholder="Notes (optional)" maxlength="5000"
                               class="form-control @error('notes', 'records') is-invalid @enderror">
                        @error('notes', 'records')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-2 d-grid">
                        <button class="btn btn-primary"><i class="bi bi-plus"></i> Add</button>
                    </div>
                </div>
            </form>
        @endcan

        @php $records = $patient->medicalRecords()->with('author')->get(); @endphp
        @if ($records->isEmpty())
            <p class="text-muted mb-0">No diagnoses yet.</p>
        @else
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead><tr><th>Date</th><th>Diagnosis</th><th>Notes</th><th>Doctor</th></tr></thead>
                    <tbody>
                    @foreach ($records as $record)
                        <tr>
                            <td class="text-nowrap">{{ $record->created_at->format('d M Y') }}</td>
                            <td class="fw-semibold">{{ $record->diagnosis }}</td>
                            <td>{{ $record->notes ?? '—' }}</td>
                            <td>{{ $record->author?->name ?? '—' }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endcan
