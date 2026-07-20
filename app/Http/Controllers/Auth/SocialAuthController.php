<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class SocialAuthController extends Controller
{
    /**
     * Redirect the user to the social provider authentication page.
     *
     * @param string $provider
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirectToProvider($locale, $provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    /**
     * Obtain the user information from social provider.
     *
     * @param string $locale
     * @param string $provider
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleProviderCallback($locale, $provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (Exception $e) {
            return redirect()->route('login.form', ['locale' => $locale])
                ->with('error', __('auth.social_login_failed'));
        }

        // Find or create the user
        $user = User::where($provider . '_id', $socialUser->getId())
            ->orWhere('email', $socialUser->getEmail())
            ->first();

        if ($user) {
            // Update social ID if not set
            if (!$user->{$provider . '_id'}) {
                $user->update([
                    $provider . '_id' => $socialUser->getId(),
                    'social_avatar' => $socialUser->getAvatar(),
                ]);
            }
        } else {
            // Create a new user
            $user = User::create([
                'name' => $socialUser->getName() ?? $socialUser->getNickname() ?? 'User',
                'email' => $socialUser->getEmail(),
                $provider . '_id' => $socialUser->getId(),
                'social_avatar' => $socialUser->getAvatar(),
                'password' => bcrypt(Str::random(24)),
                'email_verified_at' => now(),
                'is_completed' => true,
            ]);

            // Assign customer role if applicable
            if (method_exists($user, 'assignRole')) {
                $user->assignRole('customer');
            }
        }

        Auth::login($user);

        if (method_exists($user, 'hasRole') && $user->hasRole('SuperAdmin')) {
            return redirect()->route('superadmin.dashboard', ['locale' => $locale]);
        }

        return redirect()->route('dashboard', ['locale' => $locale]);
    }
}
