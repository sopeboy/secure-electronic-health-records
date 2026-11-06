<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoginController extends Controller
{
    /** FR1: after 5 wrong passwords, the email+IP is locked for 60 seconds. */
    private const MAX_ATTEMPTS  = 5;
    private const DECAY_SECONDS = 60;

    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'max:255'],
        ]);

        $key = $this->throttleKey($request);

        if (RateLimiter::tooManyAttempts($key, self::MAX_ATTEMPTS)) {
            AuditLog::record('login_locked', User::where('email', $credentials['email'])->first());

            throw ValidationException::withMessages([
                'email' => 'Too many sign-in attempts. Try again in ' . RateLimiter::availableIn($key) . ' seconds.',
            ]);
        }

        if (! Auth::attempt($credentials)) {
            RateLimiter::hit($key, self::DECAY_SECONDS);
            AuditLog::record('login_failed', User::where('email', $credentials['email'])->first());

            // Same message whether the email exists or not (no user enumeration).
            throw ValidationException::withMessages([
                'email' => 'These credentials do not match our records.',
            ]);
        }

        /** @var User $user */
        $user = Auth::user();

        // Suspended staff or staff without a role cannot sign in.
        if (! $user->isActive() || $user->role_id === null) {
            AuditLog::record('login_blocked', $user, $user);
            $this->endSession($request);

            throw ValidationException::withMessages([
                'email' => 'Your account is suspended or has no role. Contact the administrator.',
            ]);
        }

        RateLimiter::clear($key);
        $request->session()->regenerate();   // prevents session fixation
        $user->forceFill(['last_login_at' => now()])->saveQuietly();
        AuditLog::record('login', $user, $user);

        return redirect()->intended(route('dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        AuditLog::record('logout', $request->user());
        $this->endSession($request);

        return redirect()->route('login')->with('status', 'You have been signed out.');
    }

    private function endSession(Request $request): void
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }

    private function throttleKey(Request $request): string
    {
        return Str::transliterate(Str::lower((string) $request->input('email')) . '|' . $request->ip());
    }
}
