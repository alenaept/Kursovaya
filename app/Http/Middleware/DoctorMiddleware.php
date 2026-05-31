<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class DoctorMiddleware
{
    public function handle($request, Closure $next)
    {
        if (!Auth::guard('doctor')->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        return $next($request);
    }
}