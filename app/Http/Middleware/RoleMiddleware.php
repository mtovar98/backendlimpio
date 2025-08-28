<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    // Uso: ->middleware('role:1,2')  // permite Super(1) y Admin(2)
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();
        if (!$user || !in_array((string) $user->id_roles, $roles, true)) {
            return response()->json(['message' => 'Permisos no autorizados'], 403);
        }
        return $next($request);
    }
}
