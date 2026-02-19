<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    public function __construct(
        protected CustomerRepositoryInterface $customerRepository
    ) {
    }

    public function login(array $credentials, bool $remember = false): ?User
    {
        if (!Auth::attempt($credentials, $remember)) {
            return null;
        }

        return Auth::user();
    }

    public function registerCustomer(array $data): User
    {
        return $this->customerRepository->create($data);
    }

    public function redirectPath(User $user, string $locale): string
    {
        return match (true) {

            $user->hasRole('SuperAdmin')
            => route('superadmin.dashboard', ['locale' => $locale]),

            $user->hasRole('Customer')
            => route('dashboard', ['locale' => $locale]),

            default
            => route('dashboard', ['locale' => $locale]),
        };
    }
}