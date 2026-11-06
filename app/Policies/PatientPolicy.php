<?php

namespace App\Policies;

use App\Models\Patient;
use App\Models\Role;
use App\Models\User;

/**
 * Every patient-related permission in one place (Table 3.3 Role–Permission Matrix).
 * Laravel finds this policy automatically because it is named after the Patient model.
 *
 * In controllers:  Gate::authorize('prescribe', $patient);
 * In Blade:        @can('prescribe', $patient) ... @endcan
 */
class PatientPolicy
{
    /** Open the patient list (each role then sees only what Patient::visibleTo() allows). */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(Role::ADMIN, Role::DOCTOR, Role::NURSE, Role::LAB);
    }

    /** See demographics (name, number, contact). */
    public function view(User $user, Patient $patient): bool
    {
        return $user->hasRole(Role::ADMIN, Role::LAB)
            || $this->isCareTeam($user, $patient);
    }

    /** Register a patient. */
    public function create(User $user): bool
    {
        return $user->hasRole(Role::ADMIN, Role::DOCTOR, Role::NURSE);
    }

    /** Update demographics. */
    public function update(User $user, Patient $patient): bool
    {
        return $user->hasRole(Role::ADMIN) || $this->isCareTeam($user, $patient);
    }

    /** Patients are never deleted (records must be kept). */
    public function delete(User $user, Patient $patient): bool
    {
        return false;
    }

    /** Assign doctors and nurses to a patient. */
    public function assignStaff(User $user, Patient $patient): bool
    {
        return $user->hasRole(Role::ADMIN);
    }

    /** See diagnoses, notes, prescriptions and vitals. Admin: NO by default. */
    public function viewClinical(User $user, Patient $patient): bool
    {
        return $this->isCareTeam($user, $patient);
    }

    /** Create/update a diagnosis (medical record). */
    public function diagnose(User $user, Patient $patient): bool
    {
        return $user->hasRole(Role::DOCTOR) && $user->isAssignedTo($patient);
    }

    /** Issue a prescription. */
    public function prescribe(User $user, Patient $patient): bool
    {
        return $user->hasRole(Role::DOCTOR) && $user->isAssignedTo($patient);
    }

    /** Record vital signs (FR5). */
    public function recordVitals(User $user, Patient $patient): bool
    {
        return $user->hasRole(Role::NURSE) && $user->isAssignedTo($patient);
    }

    /** See lab results. Lab staff see results only, not the rest of the clinical record. */
    public function viewLabResults(User $user, Patient $patient): bool
    {
        return $user->hasRole(Role::LAB) || $this->isCareTeam($user, $patient);
    }

    /** Record a lab result (FR6). */
    public function recordLabResult(User $user, Patient $patient): bool
    {
        return $user->hasRole(Role::LAB);
    }

    /** Doctor or nurse assigned to this patient. */
    private function isCareTeam(User $user, Patient $patient): bool
    {
        return $user->hasRole(Role::DOCTOR, Role::NURSE) && $user->isAssignedTo($patient);
    }
}
