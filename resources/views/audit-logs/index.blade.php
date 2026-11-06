@extends('layouts.app')

@section('title', 'Audit logs')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <form method="GET" action="{{ route('audit-logs.index') }}" class="d-flex gap-2">
        <select name="action" class="form-select">
            <option value="">All actions</option>
            @foreach ($actions as $item)
                <option value="{{ $item }}" @selected($action === $item)>{{ $item }}</option>
            @endforeach
        </select>
        <button class="btn btn-outline-secondary"><i class="bi bi-funnel"></i> Filter</button>
    </form>
    <span class="text-muted small"><i class="bi bi-lock"></i> Read-only. Entries cannot be edited or deleted.</span>
</div>

<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-sm table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>Time</th><th>User</th><th>Action</th><th>Record</th><th>IP address</th></tr>
            </thead>
            <tbody>
            @forelse ($logs as $log)
                <tr>
                    <td class="text-nowrap small">{{ $log->created_at->format('d M Y H:i:s') }}</td>
                    <td>{{ $log->actor?->name ?? 'Unknown / guest' }}</td>
                    <td>
                        <span class="badge {{ in_array($log->action, ['access_denied', 'login_failed', 'login_locked', 'login_blocked']) ? 'text-bg-danger' : 'text-bg-light border' }}">
                            {{ $log->action }}
                        </span>
                    </td>
                    <td class="small">{{ $log->resource_type ? $log->resource_type.' #'.$log->resource_id : '—' }}</td>
                    <td class="small font-monospace">{{ $log->ip_address ?? '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted py-4">No audit entries.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">{{ $logs->links() }}</div>
@endsection
