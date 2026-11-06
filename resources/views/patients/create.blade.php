@extends('layouts.app')

@section('title', 'Register patient')

@section('content')
<div class="alert alert-warning small"><i class="bi bi-exclamation-triangle"></i> Test system: enter fake (synthetic) patient details only.</div>
<div class="card shadow-sm border-0">
    <div class="card-body">
        <form method="POST" action="{{ route('patients.store') }}">
            @csrf
            @include('patients._form', ['patient' => null])
            <div class="mt-4 d-flex gap-2">
                <button class="btn btn-primary"><i class="bi bi-check-lg"></i> Register</button>
                <a href="{{ route('patients.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
