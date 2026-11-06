@extends('layouts.app')

@section('title', 'Staff')

@section('content')
<div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-3">
    <form method="GET" action="{{ route('staff.index') }}" class="d-flex flex-wrap gap-2">
        <input type="search" name="search" value="{{ $search }}" class="form-control" placeholder="Name or email">
        <select name="role" class="form-select">
            <option value="">All roles</option>
            @foreach ($roles as $role)
                <option value="{{ $role->id }}" @selected((string) $roleId === (string) $role->id)>{{ $role->label }}</option>
            @endforeach
        </select>
        <select name="status" class="form-select">
            <option value="">Any status</option>
            <option value="active" @selected($status === 'active')>Active</option>
            <option value="suspended" @selected($status === 'suspended')>Suspended</option>
        </select>
        <button class="btn btn-outline-secondary"><i class="bi bi-search"></i></button>
    </form>
    <a href="{{ route('staff.create') }}" class="btn btn-primary"><i class="bi bi-person-plus"></i> Add staff</a>
</div>

<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Last sign-in</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($staff as $member)
                <tr>
                    <td class="fw-semibold">{{ $member->name }}</td>
                    <td>{{ $member->email }}</td>
                    <td>{{ $member->role?->label ?? '—' }}</td>
                    <td>
                        @if ($member->isActive())
                            <span class="badge text-bg-success">Active</span>
                        @else
                            <span class="badge text-bg-secondary">Suspended</span>
                        @endif
                    </td>
                    <td class="small text-muted">{{ $member->last_login_at?->diffForHumans() ?? 'Never' }}</td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('staff.edit', $member) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        @unless ($member->is(auth()->user()))
                            <form method="POST" action="{{ route('staff.toggle-status', $member) }}" class="d-inline"
                                  data-confirm="{{ $member->isActive() ? 'Suspend '.$member->name.'? They will be signed out immediately.' : 'Re-activate '.$member->name.'?' }}">
                                @csrf
                                @method('PATCH')
                                @if ($member->isActive())
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-person-slash"></i> Suspend</button>
                                @else
                                    <button class="btn btn-sm btn-outline-success"><i class="bi bi-person-check"></i> Activate</button>
                                @endif
                            </form>
                        @endunless
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-4">No staff found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">{{ $staff->links() }}</div>
@endsection

@push('scripts')
<script src="{{ asset('js/sehrs.js') }}"></script>
@endpush
