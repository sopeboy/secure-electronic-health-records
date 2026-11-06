<?php

namespace App\Providers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Permissions that are not about one patient.
        Gate::define('manage-staff',    fn (User $user) => $user->hasRole(Role::ADMIN));
        Gate::define('view-audit-logs', fn (User $user) => $user->hasRole(Role::ADMIN));
        Gate::define('view-reports',    fn (User $user) => $user->hasRole(Role::ADMIN, Role::DOCTOR));

        // Password rule used when staff accounts are created (NIST SP 800-63B: length over complexity).
        Password::defaults(fn () => Password::min(10)->letters()->numbers());

        // Pagination links styled for Bootstrap 5.
        Paginator::useBootstrapFive();
    }
}
