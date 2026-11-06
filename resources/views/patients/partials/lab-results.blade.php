@can('viewLabResults', $patient)
<div class="card shadow-sm border-0 mb-3" id="lab-results">
    <div class="card-header bg-white fw-semibold"><i class="bi bi-clipboard2-pulse"></i> Lab results</div>
    <div class="card-body">
        @can('recordLabResult', $patient)
            <form method="POST" action="{{ route('patients.lab-results.store', $patient) }}" class="mb-3">
                @csrf
                <div class="row g-2">
                    <div class="col-md-4">
                        <input name="test_name" value="{{ old('test_name') }}" placeholder="Test e.g. Malaria parasite" required maxlength="150"
                               class="form-control @error('test_name', 'labResults') is-invalid @enderror">
                        @error('test_name', 'labResults')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <input name="result" value="{{ old('result') }}" placeholder="Result" required maxlength="2000"
                               class="form-control @error('result', 'labResults') is-invalid @enderror">
                        @error('result', 'labResults')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-2 d-grid">
                        <button class="btn btn-primary"><i class="bi bi-plus"></i> Add</button>
                    </div>
                </div>
            </form>
        @endcan

        @php $labResults = $patient->labResults()->with('recorder')->get(); @endphp
        @if ($labResults->isEmpty())
            <p class="text-muted mb-0">No lab results yet.</p>
        @else
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead><tr><th>Date</th><th>Test</th><th>Result</th><th>Recorded by</th></tr></thead>
                    <tbody>
                    @foreach ($labResults as $lab)
                        <tr>
                            <td class="text-nowrap">{{ $lab->created_at->format('d M Y') }}</td>
                            <td class="fw-semibold">{{ $lab->test_name }}</td>
                            <td>{{ $lab->result }}</td>
                            <td>{{ $lab->recorder?->name ?? ($lab->source === 'api' ? 'Lab system (API)' : '—') }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endcan
