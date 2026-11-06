@extends('layouts.app')

@section('title', 'Edit staff')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-body">
        <form method="POST" action="{{ route('staff.update', $staffMember) }}">
            @csrf
            @method('PUT')
            @include('staff._form', ['staffMember' => $staffMember])
            <div class="mt-4 d-flex gap-2">
                <button class="btn btn-primary"><i class="bi bi-check-lg"></i> Save changes</button>
                <a href="{{ route('staff.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
