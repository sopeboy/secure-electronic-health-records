<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\LabResult;
use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\Role;
use App\Models\User;
use App\Models\Vital;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();

        // Each role only sees counts that match what it is allowed to do.
        $cards = match ($user->role?->name) {
            Role::ADMIN => [
                ['Staff accounts',     User::count(),                                            'bi-person-badge'],
                ['Suspended staff',    User::where('status', User::STATUS_SUSPENDED)->count(),  'bi-person-slash'],
                ['Registered patients', Patient::count(),                                        'bi-people'],
                ['Audit events today', AuditLog::whereDate('created_at', today())->count(),      'bi-shield-lock'],
            ],
            Role::DOCTOR => [
                ['My patients',           $user->assignedPatients()->count(),                      'bi-people'],
                ['Records I wrote',       MedicalRecord::where('created_by', $user->id)->count(),  'bi-journal-medical'],
                ['Prescriptions issued',  Prescription::where('prescribed_by', $user->id)->count(), 'bi-capsule'],
            ],
            Role::NURSE => [
                ['My patients',           $user->assignedPatients()->count(),                      'bi-people'],
                ['Vitals I recorded',     Vital::where('recorded_by', $user->id)->count(),          'bi-heart-pulse'],
            ],
            Role::LAB => [
                ['Results recorded today', LabResult::where('recorded_by', $user->id)->whereDate('created_at', today())->count(), 'bi-clipboard2-pulse'],
                ['Results recorded (all)', LabResult::where('recorded_by', $user->id)->count(), 'bi-clipboard-data'],
            ],
            default => [],
        };

        return view('dashboard', compact('user', 'cards'));
    }
}
