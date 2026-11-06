<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Route-level role check. Usage in routes:  ->middleware('role:admin')  or  'role:doctor,nurse'
 * Denials become HTTP 403 and are written to the audit log (see bootstrap/app.php).
 */
class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! $request->user()?->hasRole(...$roles)) {
            abort(403, 'Your role does not have access to this page.');
        }

        return $next($request);
    }
}
