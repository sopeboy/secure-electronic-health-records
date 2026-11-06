<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\LabResult;
use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\Vital;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * Adds clinical entries to a patient. Entries are only added, never edited or deleted,
 * so the history of the patient's care is always kept.
 */
class ClinicalEntryController extends Controller
{
    /** Doctor adds a diagnosis (FR4). */
    public function storeRecord(Request $request, Patient $patient): RedirectResponse
    {
        Gate::authorize('diagnose', $patient);

        $data = $request->validateWithBag('records', [
            'diagnosis' => ['required', 'string', 'max:2000'],
            'notes'     => ['nullable', 'string', 'max:5000'],
        ]);

        $entry = new MedicalRecord($data);
        $entry->patient_id = $patient->id;
        $entry->created_by = $request->user()->id;
        $entry->save();

        return $this->done($entry, $patient, 'records', 'Diagnosis saved.');
    }

    /** Nurse records vital signs (FR5). */
    public function storeVitals(Request $request, Patient $patient): RedirectResponse
    {
        Gate::authorize('recordVitals', $patient);

        $data = $request->validateWithBag('vitals', [
            'temperature'    => ['nullable', 'numeric', 'between:30,45', 'required_without_all:blood_pressure,pulse,weight'],
            'blood_pressure' => ['nullable', 'regex:/^\d{2,3}\/\d{2,3}$/'],
            'pulse'          => ['nullable', 'integer', 'between:20,250'],
            'weight'         => ['nullable', 'numeric', 'between:0.5,400'],
        ], [
            'temperature.required_without_all' => 'Enter at least one vital sign.',
            'blood_pressure.regex'             => 'Use the format 120/80.',
        ]);

        $entry = new Vital($data);
        $entry->patient_id = $patient->id;
        $entry->recorded_by = $request->user()->id;
        $entry->save();

        return $this->done($entry, $patient, 'vitals', 'Vital signs saved.');
    }

    /** Doctor issues a prescription (FR4). */
    public function storePrescription(Request $request, Patient $patient): RedirectResponse
    {
        Gate::authorize('prescribe', $patient);

        $data = $request->validateWithBag('prescriptions', [
            'medication'   => ['required', 'string', 'max:255'],
            'dosage'       => ['required', 'string', 'max:255'],
            'instructions' => ['nullable', 'string', 'max:500'],
        ]);

        $entry = new Prescription($data);
        $entry->patient_id = $patient->id;
        $entry->prescribed_by = $request->user()->id;
        $entry->save();

        return $this->done($entry, $patient, 'prescriptions', 'Prescription saved.');
    }

    /** Lab staff record a test result (FR6). */
    public function storeLabResult(Request $request, Patient $patient): RedirectResponse
    {
        Gate::authorize('recordLabResult', $patient);

        $data = $request->validateWithBag('labResults', [
            'test_name' => ['required', 'string', 'max:150'],
            'result'    => ['required', 'string', 'max:2000'],
        ]);

        $entry = new LabResult($data);
        $entry->patient_id = $patient->id;
        $entry->recorded_by = $request->user()->id;
        $entry->source = 'staff';
        $entry->save();

        return $this->done($entry, $patient, 'lab-results', 'Lab result saved.');
    }

    private function done($entry, Patient $patient, string $section, string $message): RedirectResponse
    {
        AuditLog::record('created', $entry);

        return redirect()->to(route('patients.show', $patient) . '#' . $section)->with('status', $message);
    }
}
