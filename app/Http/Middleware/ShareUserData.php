<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class ShareUserData
{
    public function handle(Request $request, Closure $next)
    {
        // Определяем текущий guard и пользователя
        if (Auth::guard('admin')->check()) {
            $user = Auth::guard('admin')->user();
            $userType = 'admin';
        } elseif (Auth::guard('doctor')->check()) {
            $user = Auth::guard('doctor')->user();
            $userType = 'doctor';
        } else {
            $user = Auth::user();
            $userType = 'user';
        }
        
        Inertia::share([
            'auth' => [
                'user' => $user,
                'userType' => $userType,
            ],
        ]);
        
        return $next($request);
    }
}
