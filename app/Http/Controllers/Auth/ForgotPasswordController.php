<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm(string $locale): View
    {
        return view('auth.passwords.email', ['activeLocale' => $locale]);
    }

    public function sendResetLinkEmail(ForgotPasswordRequest $request, string $locale): RedirectResponse
    {
        $status = Password::sendResetLink(
            $request->only('email'),
            fn ($user, string $token) => $user->notify(new ResetPasswordNotification($token, $locale))
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)])->onlyInput('email');
    }

    public function showResetForm(string $locale, string $token): View
    {
        return view('auth.passwords.reset', [
            'activeLocale' => $locale,
            'token' => $token,
            'email' => request('email'),
        ]);
    }

    public function reset(ResetPasswordRequest $request, string $locale): RedirectResponse
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, string $password): void {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login.form', ['locale' => $locale])->with('status', __($status))
            : back()->withErrors(['email' => __($status)])->withInput($request->only('email'));
    }
}
