@extends('layouts.app')

@section('title', 'Add staff')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-body">
        <form method="POST" action="{{ route('staff.store') }}">
            @csrf
            @include('staff._form', ['staffMember' => null])
            <div class="mt-4 d-flex gap-2">
                <button class="btn btn-primary"><i class="bi bi-check-lg"></i> Create account</button>
                <a href="{{ route('staff.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
