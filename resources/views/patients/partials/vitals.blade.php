@can('viewClinical', $patient)
<div class="card shadow-sm border-0 mb-3" id="vitals">
    <div class="card-header bg-white fw-semibold"><i class="bi bi-heart-pulse"></i> Vital signs</div>
    <div class="card-body">
        @can('recordVitals', $patient)
            <form method="POST" action="{{ route('patients.vitals.store', $patient) }}" class="mb-3">
                @csrf
                <div class="row g-2">
                    <div class="col-6 col-md-2">
                        <input name="temperature" value="{{ old('temperature') }}" placeholder="Temp °C" inputmode="decimal"
                               class="form-control @error('temperature', 'vitals') is-invalid @enderror">
                    </div>
                    <div class="col-6 col-md-3">
                        <input name="blood_pressure" value="{{ old('blood_pressure') }}" placeholder="BP e.g. 120/80"
                               class="form-control @error('blood_pressure', 'vitals') is-invalid @enderror">
                    </div>
                    <div class="col-6 col-md-2">
                        <input name="pulse" value="{{ old('pulse') }}" placeholder="Pulse" inputmode="numeric"
                               class="form-control @error('pulse', 'vitals') is-invalid @enderror">
                    </div>
                    <div class="col-6 col-md-3">
                        <input name="weight" value="{{ old('weight') }}" placeholder="Weight kg" inputmode="decimal"
                               class="form-control @error('weight', 'vitals') is-invalid @enderror">
                    </div>
                    <div class="col-md-2 d-grid">
                        <button class="btn btn-primary"><i class="bi bi-plus"></i> Add</button>
                    </div>
                </div>
                @if ($errors->vitals->any())
                    <div class="text-danger small mt-1">{{ $errors->vitals->first() }}</div>
                @endif
            </form>
        @endcan

        @php $vitals = $patient->vitals()->with('recorder')->get(); @endphp
        @if ($vitals->isEmpty())
            <p class="text-muted mb-0">No vital signs yet.</p>
        @else
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead><tr><th>Date</th><th>Temp (°C)</th><th>BP</th><th>Pulse</th><th>Weight (kg)</th><th>Nurse</th></tr></thead>
                    <tbody>
                    @foreach ($vitals as $vital)
                        <tr>
                            <td class="text-nowrap">{{ $vital->created_at->format('d M Y H:i') }}</td>
                            <td>{{ $vital->temperature ?? '—' }}</td>
                            <td>{{ $vital->blood_pressure ?? '—' }}</td>
                            <td>{{ $vital->pulse ?? '—' }}</td>
                            <td>{{ $vital->weight ?? '—' }}</td>
                            <td>{{ $vital->recorder?->name ?? '—' }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endcan
