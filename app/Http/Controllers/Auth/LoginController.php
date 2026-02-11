<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    // Show Login Page
    public function showLoginForm()
    {
        return view('auth.login');

    }



    public function login(Request $request, string $locale)
    {
        // Validation
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Attempt login
        if (!Auth::attempt($validated, $request->boolean('remember'))) {
            return back()
                ->withErrors([
                    'email' => __('auth.invalid_credentials'),
                ])
                ->withInput();
        }

        $request->session()->regenerate();

        $user = $request->user();
        $defaultRoute = $user->hasRole('SuperAdmin')
            ? route('superadmin.dashboard', ['locale' => $locale])
            : route('dashboard', ['locale' => $locale]);

        return redirect()->intended($defaultRoute);
    }

    public function logout(Request $request, $locale)
    {
        Auth::logout(); // Logout user
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login.form', ['locale' => $locale]);
    }

}
