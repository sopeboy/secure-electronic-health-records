<?php

namespace Tests\Feature;

use App\Models\LabResult;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/** Table 3.7: API security. */
class LabApiTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;   // runs DatabaseSeeder (roles, demo staff, 20 synthetic patients)

    private function payload(): array
    {
        return [
            'patient_number' => Patient::firstOrFail()->patient_number,
            'test_name'      => 'Widal test',
            'result'         => 'Negative',
        ];
    }

    public function test_request_without_token_is_rejected(): void
    {
        $this->postJson('/api/lab-results', $this->payload())->assertUnauthorized();
        $this->post('/api/lab-results', $this->payload())->assertUnauthorized();   // even without Accept: json
    }

    public function test_lab_token_can_record_result(): void
    {
        Sanctum::actingAs(User::where('email', 'lab@sehrs.test')->first(), ['lab-results:create']);

        $this->postJson('/api/lab-results', $this->payload())
            ->assertCreated()
            ->assertJsonMissing(['result' => 'Negative']);

        $this->assertSame('api', LabResult::latest('id')->first()->source);
        $this->assertDatabaseHas('audit_logs', ['action' => 'created', 'resource_type' => 'LabResult']);
    }

    public function test_token_without_lab_ability_is_forbidden(): void
    {
        Sanctum::actingAs(User::where('email', 'lab@sehrs.test')->first(), []);

        $this->postJson('/api/lab-results', $this->payload())->assertForbidden();
    }

    public function test_non_lab_user_token_is_forbidden(): void
    {
        Sanctum::actingAs(User::where('email', 'nurse@sehrs.test')->first(), ['lab-results:create']);

        $this->postJson('/api/lab-results', $this->payload())->assertForbidden();
    }

    public function test_unknown_patient_is_rejected(): void
    {
        Sanctum::actingAs(User::where('email', 'lab@sehrs.test')->first(), ['lab-results:create']);

        $this->postJson('/api/lab-results', ['patient_number' => "' OR 1=1 --"] + $this->payload())
            ->assertUnprocessable()
            ->assertJsonValidationErrors('patient_number');
    }
}
