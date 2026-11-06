<?php

namespace Tests\Feature;

use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Table 3.7: Role-based access and patient authorization (Table 3.3 matrix). */
class AccessControlTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;   // runs DatabaseSeeder (roles, demo staff, 20 synthetic patients)

    private function user(string $email): User
    {
        return User::where('email', $email)->firstOrFail();
    }

    private function assignedPatient(User $user): Patient
    {
        return $user->assignedPatients()->firstOrFail();
    }

    private function unassignedPatient(User $user): Patient
    {
        return Patient::whereDoesntHave('assignedStaff', fn ($q) => $q->where('users.id', $user->id))->firstOrFail();
    }

    public function test_nurse_cannot_open_admin_page_and_attempt_is_logged(): void
    {
        $nurse = $this->user('nurse@sehrs.test');

        $this->actingAs($nurse)->get('/staff')->assertForbidden();

        $this->assertDatabaseHas('audit_logs', ['action' => 'access_denied', 'actor_id' => $nurse->id]);
    }

    public function test_admin_can_open_staff_page(): void
    {
        $this->actingAs($this->user('admin@sehrs.test'))->get('/staff')->assertOk();
    }

    public function test_doctor_can_open_assigned_patient(): void
    {
        $doctor = $this->user('doctor@sehrs.test');

        $this->actingAs($doctor)->get('/patients/' . $this->assignedPatient($doctor)->id)->assertOk();
    }

    public function test_doctor_cannot_open_unassigned_patient(): void
    {
        $doctor = $this->user('doctor@sehrs.test');
        $patient = $this->unassignedPatient($doctor);

        $this->actingAs($doctor)->get('/patients/' . $patient->id)->assertForbidden();

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'access_denied', 'resource_type' => 'Patient', 'resource_id' => $patient->id,
        ]);
    }

    public function test_doctor_only_lists_assigned_patients(): void
    {
        $doctor = $this->user('doctor@sehrs.test');
        $hidden = $this->unassignedPatient($doctor);

        $this->actingAs($doctor)->get('/patients')
            ->assertOk()
            ->assertDontSee($hidden->patient_number);
    }

    public function test_admin_does_not_see_clinical_records(): void
    {
        $patient = Patient::firstOrFail();
        $diagnosis = MedicalRecord::where('patient_id', $patient->id)->firstOrFail()->diagnosis;

        $this->actingAs($this->user('admin@sehrs.test'))->get('/patients/' . $patient->id)
            ->assertOk()
            ->assertDontSee('Diagnoses')
            ->assertDontSee($diagnosis);
    }

    public function test_nurse_cannot_prescribe(): void
    {
        $nurse = $this->user('nurse@sehrs.test');
        $patient = $this->assignedPatient($nurse);

        $this->actingAs($nurse)
            ->post("/patients/{$patient->id}/prescriptions", ['medication' => 'X', 'dosage' => 'Y'])
            ->assertForbidden();
    }

    public function test_lab_staff_cannot_add_diagnosis(): void
    {
        $patient = Patient::firstOrFail();

        $this->actingAs($this->user('lab@sehrs.test'))
            ->post("/patients/{$patient->id}/records", ['diagnosis' => 'X'])
            ->assertForbidden();
    }

    public function test_doctor_can_add_diagnosis_to_assigned_patient(): void
    {
        $doctor = $this->user('doctor@sehrs.test');
        $patient = $this->assignedPatient($doctor);

        $this->actingAs($doctor)
            ->post("/patients/{$patient->id}/records", ['diagnosis' => 'Test diagnosis'])
            ->assertRedirect();

        $this->assertSame('Test diagnosis', MedicalRecord::latest('id')->first()->diagnosis);
    }

    public function test_doctor_cannot_open_audit_logs(): void
    {
        $this->actingAs($this->user('doctor@sehrs.test'))->get('/audit-logs')->assertForbidden();
    }
}
