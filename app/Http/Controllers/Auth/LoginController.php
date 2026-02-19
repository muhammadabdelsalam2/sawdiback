<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function __construct(
        protected AuthService $authService
    ) {

    }
    // Show Login Page
    public function showLoginForm()
    {
        return view('auth.login');

    }



    public function login(Request $request, string $locale, AuthService $authService)
    {
        // Validate
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Attempt login via service
        $user = $authService->login(
            credentials: $credentials,
            remember: $request->boolean('remember')
        );

        // Failed login
        if (!$user) {
            return back()
                ->withErrors([
                    'email' => __('auth.invalid_credentials'),
                ])
                ->onlyInput('email');
        }

        // Prevent session fixation
        $request->session()->regenerate();

        // Redirect
        return redirect()->intended(
            $authService->redirectPath($user, $locale)
        );
    }



    /*
   |--------------------------------------------------------------------------
   | Show Register Form
   |--------------------------------------------------------------------------
   */
    public function showRegister(string $locale)
    {
        return view('auth.customer.register', compact('locale'));
    }

    /*
    |--------------------------------------------------------------------------
    | Register Customer
    |--------------------------------------------------------------------------
    */
    public function register(Request $request, string $locale)
    {
        $data = $request->validate([
            'name' => ['required'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'min:6', 'confirmed'],
            'terms' => ['required', 'accepted'],
        ]);

        $user = $this->authService->registerCustomer($data);

        Auth::login($user);

        return redirect()->route('dashboard', [
            'locale' => $locale
        ]);
    }

    public function store()
    {
    }

    public function logout(Request $request, $locale)
    {
        Auth::logout(); // Logout user
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login.form', ['locale' => $locale]);
    }

}
