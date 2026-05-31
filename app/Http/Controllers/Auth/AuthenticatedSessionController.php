<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Admin;
use App\Models\Doctor;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
   
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    
    public function store(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');
        $email = $request->email;


        if (Auth::guard('admin')->attempt($credentials, $remember)) {
            $request->session()->regenerate();
            session(['user_type' => 'admin']);
            return redirect()->intended(route('admin.dashboard'));
        }
        

        if (Auth::guard('doctor')->attempt($credentials, $remember)) {
            $request->session()->regenerate();
            session(['user_type' => 'doctor']);
            return redirect()->route('doctor.dashboard');


            
        }
        

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            session(['user_type' => 'user']);
            return redirect()->intended(route('dashboard'));
        }
        

        throw ValidationException::withMessages([
            'email' => __('auth.failed'),
        ]);
    }

   
    public function destroy(Request $request): RedirectResponse
    {
        $userType = session('user_type');
        
        match($userType) {
            'admin' => Auth::guard('admin')->logout(),
            'doctor' => Auth::guard('doctor')->logout(),
            default => Auth::guard('web')->logout(),
        };

        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $request->session()->forget('user_type');

        return redirect('/');
    }
}
