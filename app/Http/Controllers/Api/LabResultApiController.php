<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\LabResult;
use App\Models\Patient;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * POST /api/lab-results — lets the (simulated) laboratory system send a result.
 * Needs a Sanctum token from an active lab staff account with the "lab-results:create" ability.
 */
class LabResultApiController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user->tokenCan('lab-results:create') || ! $user->hasRole(Role::LAB) || ! $user->isActive()) {
            abort(403, 'This token is not allowed to record lab results.');
        }

        $data = $request->validate([
            'patient_number' => ['required', 'string', 'max:20', 'exists:patients,patient_number'],
            'test_name'      => ['required', 'string', 'max:150'],
            'result'         => ['required', 'string', 'max:2000'],
        ]);

        $patient = Patient::where('patient_number', $data['patient_number'])->firstOrFail();

        $labResult = new LabResult(['test_name' => $data['test_name'], 'result' => $data['result']]);
        $labResult->patient_id = $patient->id;
        $labResult->recorded_by = $user->id;
        $labResult->source = 'api';
        $labResult->save();

        AuditLog::record('created', $labResult, $user);

        // The result itself is not echoed back.
        return response()->json([
            'message'        => 'Lab result recorded.',
            'id'             => $labResult->id,
            'patient_number' => $patient->patient_number,
            'test_name'      => $labResult->test_name,
            'recorded_at'    => $labResult->created_at->toIso8601String(),
        ], 201);
    }
}
