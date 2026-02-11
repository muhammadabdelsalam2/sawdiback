<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserManagementController extends Controller
{
    public function index(Request $request): View
    {
        $users = User::query()
            ->with(['roles', 'permissions'])
            ->orderBy('id')
            ->paginate(10)
            ->withQueryString();

        return view('dashboard.superadmin.users.index', [
            'users' => $users,
        ]);
    }

    public function create(): View
    {
        return view('dashboard.superadmin.users.create', [
            'roles' => Role::query()->orderBy('name')->get(),
            'permissions' => Permission::query()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request, string $locale): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['required', 'string', Rule::exists('roles', 'name')],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['required', 'string', Rule::exists('permissions', 'name')],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
        ]);

        $user->syncRoles($validated['roles']);
        $user->syncPermissions($validated['permissions'] ?? []);

        return redirect()
            ->route('superadmin.users.index', ['locale' => $locale])
            ->with('success', 'User created successfully.');
    }

    public function edit(string $locale, User $user): View
    {
        return view('dashboard.superadmin.users.edit', [
            'userModel' => $user->load(['roles', 'permissions']),
            'roles' => Role::query()->orderBy('name')->get(),
            'permissions' => Permission::query()->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, string $locale, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['required', 'string', Rule::exists('roles', 'name')],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['required', 'string', Rule::exists('permissions', 'name')],
        ]);

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        if (!empty($validated['password'])) {
            $userData['password'] = $validated['password'];
        }

        $user->update($userData);
        $user->syncRoles($validated['roles']);
        $user->syncPermissions($validated['permissions'] ?? []);

        return redirect()
            ->route('superadmin.users.index', ['locale' => $locale])
            ->with('success', 'User updated successfully.');
    }

    public function destroy(string $locale, User $user): RedirectResponse
    {
        if ($user->is(auth()->user())) {
            return redirect()
                ->route('superadmin.users.index', ['locale' => $locale])
                ->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()
            ->route('superadmin.users.index', ['locale' => $locale])
            ->with('success', 'User deleted successfully.');
    }
}
