@extends('layouts.app')

@section('title', 'Edit patient')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-body">
        <p class="text-muted mb-3">Patient No. <span class="font-monospace">{{ $patient->patient_number }}</span></p>
        <form method="POST" action="{{ route('patients.update', $patient) }}">
            @csrf
            @method('PUT')
            @include('patients._form', ['patient' => $patient])
            <div class="mt-4 d-flex gap-2">
                <button class="btn btn-primary"><i class="bi bi-check-lg"></i> Save changes</button>
                <a href="{{ route('patients.show', $patient) }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
