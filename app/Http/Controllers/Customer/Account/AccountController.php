<?php

namespace App\Http\Controllers\Customer\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\Account\UpdatePasswordRequest;
use App\Http\Requests\Customer\Account\UpdateProfileRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function profile(string $locale): View
    {
        $activeLocale = $locale;
        $user = Auth::user();
        abort_unless($user instanceof User, 403);

        return view('dashboard.customer.account.profile', compact('activeLocale', 'user'));
    }

    public function updateProfile(UpdateProfileRequest $request, string $locale): RedirectResponse
    {
        $user = Auth::user();
        abort_unless($user instanceof User, 403);

        $data = $request->validated();

        if ($request->hasFile('avatar')) {
            if ($user->avatar && !filter_var($user->avatar, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete($user->avatar);
            }

            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($data);

        return redirect()
            ->route('customer.profile.show', ['locale' => $locale])
            ->with('success', __('account.profile.updated'));
    }

    public function password(string $locale): View
    {
        $activeLocale = $locale;

        return view('dashboard.customer.account.password', compact('activeLocale'));
    }

    public function updatePassword(UpdatePasswordRequest $request, string $locale): RedirectResponse
    {
        $user = Auth::user();
        abort_unless($user instanceof User, 403);

        $validated = $request->validated();

        if (!Hash::check($validated['current_password'], (string) $user->password)) {
            return back()
                ->withErrors(['current_password' => __('account.password.current_incorrect')])
                ->withInput();
        }

        $user->update([
            'password' => $validated['password'],
        ]);

        return redirect()
            ->route('customer.password.show', ['locale' => $locale])
            ->with('success', __('account.password.updated'));
    }
}
