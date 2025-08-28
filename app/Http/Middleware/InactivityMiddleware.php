<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;



class InactivityMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        
        $user  = $request->user();
        $token = $user?->currentAccessToken(); // Sanctum token

        Log::info('InactivityMiddleware ejecutado', [
    'user_id' => $user?->id_users,
    'token_id' => $token?->id,
    'last_used_at' => $token?->last_used_at,
]);

        if ($token) {
            $limit = (int) env('INACTIVITY_MINUTES', 1); // 2 horas por defecto
            $last  = $token->last_used_at ?? $token->created_at; // fallback

            $token->forceFill(['last_used_at' => now()->subMinutes(10)])->save();
            if ($last && now()->diffInRealSeconds($last) >= ($limit * 60)) {
                // revocar y forzar re-login
                $token->delete();
                return response()->json(['message' => 'Sesión expirada por inactividad'], 401);
            }
        }

        if ($token) {
            $token->forceFill(['last_used_at' => now()])->save();
        }

        return $next($request);
    }
}
