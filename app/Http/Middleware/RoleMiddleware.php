<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (
            ! $user ||
            ! $user->role instanceof UserRole ||
            ! in_array($user->role->value, $roles, true)
        ) {
            abort(403, 'Anda tidak memiliki hak akses.');
        }

        return $next($request);
    }
}
