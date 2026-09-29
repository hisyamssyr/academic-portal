<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CekPeran
{
    /**
     * Restrict the request to the given roles, defaulting to the admin roles.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        $allowedRoles = $roles === [] ? UserRole::staffValues() : $roles;

        abort_unless(in_array($request->user()->role->value, $allowedRoles, true), 403);

        return $next($request);
    }
}
