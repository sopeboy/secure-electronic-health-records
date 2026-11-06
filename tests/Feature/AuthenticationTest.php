<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Table 3.7: Authentication and session security. */
class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;   // runs DatabaseSeeder (roles, demo staff, 20 synthetic patients)

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_correct_password_signs_in(): void
    {
        $this->post('/login', ['email' => 'admin@sehrs.test', 'password' => 'password'])
            ->assertRedirect('/dashboard');

        $this->assertAuthenticated();
        $this->assertDatabaseHas('audit_logs', ['action' => 'login']);
    }

    public function test_wrong_password_is_denied_and_logged(): void
    {
        $this->post('/login', ['email' => 'admin@sehrs.test', 'password' => 'wrong-password'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
        $this->assertDatabaseHas('audit_logs', ['action' => 'login_failed']);
    }

    public function test_login_is_throttled_after_five_failures(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', ['email' => 'admin@sehrs.test', 'password' => 'wrong']);
        }

        // Even the correct password is refused while locked.
        $this->post('/login', ['email' => 'admin@sehrs.test', 'password' => 'password'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
        $this->assertDatabaseHas('audit_logs', ['action' => 'login_locked']);
    }

    public function test_suspended_user_cannot_sign_in(): void
    {
        User::where('email', 'nurse@sehrs.test')->update(['status' => User::STATUS_SUSPENDED]);

        $this->post('/login', ['email' => 'nurse@sehrs.test', 'password' => 'password'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_signed_in_user_is_removed_when_suspended(): void
    {
        $nurse = User::where('email', 'nurse@sehrs.test')->first();
        $this->actingAs($nurse)->get('/dashboard')->assertOk();

        $nurse->status = User::STATUS_SUSPENDED;
        $nurse->save();

        $this->get('/dashboard')->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_logout_ends_the_session(): void
    {
        $admin = User::where('email', 'admin@sehrs.test')->first();

        $this->actingAs($admin)->post('/logout')->assertRedirect('/login');
        $this->assertGuest();
        $this->get('/dashboard')->assertRedirect('/login');
        $this->assertSame(1, AuditLog::where('action', 'logout')->count());
    }
}
