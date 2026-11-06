<?php

namespace App\Http\Requests;

use App\Models\Patient;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation for registering and updating patients (FR3).
 * Permission is checked BEFORE validation, so an unauthorized user gets 403, not form errors.
 */
class PatientRequest extends FormRequest
{
    public function authorize(): bool
    {
        $patient = $this->route('patient');

        return $patient instanceof Patient
            ? $this->user()->can('update', $patient)
            : $this->user()->can('create', Patient::class);
    }

    public function rules(): array
    {
        return [
            'first_name'    => ['required', 'string', 'max:100'],
            'last_name'     => ['required', 'string', 'max:100'],
            'gender'        => ['required', 'in:male,female'],
            'date_of_birth' => ['required', 'date', 'after:1900-01-01', 'before_or_equal:today'],
            'phone'         => ['nullable', 'string', 'regex:/^[0-9+\-\s]{7,20}$/'],
            'address'       => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return ['phone.regex' => 'Phone may only contain digits, spaces, + and - (7–20 characters).'];
    }
}
