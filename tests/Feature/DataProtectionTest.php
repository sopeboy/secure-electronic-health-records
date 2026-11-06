<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use LogicException;
use Tests\TestCase;

/** Table 3.7: Encryption at rest, input validation, audit logging, security headers. */
class DataProtectionTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;   // runs DatabaseSeeder (roles, demo staff, 20 synthetic patients)

    public function test_sensitive_fields_are_unreadable_in_the_database(): void
    {
        $record = new MedicalRecord(['diagnosis' => 'SecretDiagnosisXYZ', 'notes' => 'SecretNotesXYZ']);
        $record->patient_id = Patient::firstOrFail()->id;
        $record->save();

        $raw = DB::table('medical_records')->where('id', $record->id)->first();

        $this->assertStringNotContainsString('SecretDiagnosisXYZ', $raw->diagnosis);
        $this->assertStringNotContainsString('SecretNotesXYZ', $raw->notes);
        $this->assertSame('SecretDiagnosisXYZ', $record->fresh()->diagnosis);
    }

    public function test_viewing_and_updating_a_patient_are_both_logged(): void
    {
        $admin = User::where('email', 'admin@sehrs.test')->first();
        $patient = Patient::firstOrFail();

        $this->actingAs($admin)->get("/patients/{$patient->id}")->assertOk();
        $this->actingAs($admin)->put("/patients/{$patient->id}", [
            'first_name' => 'Updated', 'last_name' => $patient->last_name, 'gender' => $patient->gender,
            'date_of_birth' => $patient->date_of_birth->toDateString(),
        ])->assertRedirect();

        $this->assertDatabaseHas('audit_logs', ['action' => 'viewed', 'resource_id' => $patient->id]);
        $this->assertDatabaseHas('audit_logs', ['action' => 'updated', 'resource_id' => $patient->id]);
    }

    public function test_audit_logs_cannot_be_changed_or_deleted(): void
    {
        $log = AuditLog::create(['action' => 'test']);

        $this->expectException(LogicException::class);
        $log->update(['action' => 'tampered']);
    }

    public function test_malformed_and_injection_input_is_rejected(): void
    {
        $admin = User::where('email', 'admin@sehrs.test')->first();

        $this->actingAs($admin)->post('/patients', [
            'first_name'    => "Robert'); DROP TABLE patients;--",
            'last_name'     => '',
            'gender'        => 'unknown',
            'date_of_birth' => 'not-a-date',
            'phone'         => '<script>alert(1)</script>',
        ])->assertSessionHasErrors(['last_name', 'gender', 'date_of_birth', 'phone']);

        $this->assertSame(20, Patient::count());   // nothing saved, table still there
    }

    public function test_security_headers_are_sent(): void
    {
        $this->get('/login')
            ->assertHeader('X-Frame-Options', 'DENY')
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('Content-Security-Policy');
    }
}
