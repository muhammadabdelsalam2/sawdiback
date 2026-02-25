<?php

namespace App\Repositories\Contracts;

use App\DTOs\Auth\LoginDTO;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface UserRepositoryInterface
{
    /*
    |--------------------------------------------------------------------------
    | Find Methods
    |--------------------------------------------------------------------------
    */

    public function findById(int $id): ?User;

    public function findByEmail(string $email): ?User;

    public function findByPhone(string $phone): ?User;

    public function findByIdentifier(LoginDTO $dto): ?User;

    public function findByProvider(string $provider, string $providerId): ?User;


    /*
    |--------------------------------------------------------------------------
    | Exists Methods
    |--------------------------------------------------------------------------
    */

    public function existsByEmail(string $email): bool;

    public function existsByPhone(string $phone): bool;


    /*
    |--------------------------------------------------------------------------
    | Create / Update
    |--------------------------------------------------------------------------
    */

    public function create(array $data): User;

    public function update(User $user, array $data): bool;

    public function updateById(int $id, array $data): bool;


    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */

    public function delete(User $user): bool;

    public function deleteById(int $id): bool;


    /*
    |--------------------------------------------------------------------------
    | Query Methods
    |--------------------------------------------------------------------------
    */

    public function getAll(): Collection;

    public function paginate(int $perPage = 15);


    /*
    |--------------------------------------------------------------------------
    | Role Methods (Spatie)
    |--------------------------------------------------------------------------
    */

    public function assignRole(User $user, string $role): void;

    public function syncRoles(User $user, array $roles): void;

    public function hasRole(User $user, string $role): bool;


    /*
    |--------------------------------------------------------------------------
    | Social Login Methods
    |--------------------------------------------------------------------------
    */

    public function updateProvider(
        User $user,
        string $provider,
        string $providerId,
        ?string $avatar = null
    ): bool;


    /*
    |--------------------------------------------------------------------------
    | Token Methods (Sanctum)
    |--------------------------------------------------------------------------
    */

    public function createToken(User $user, string $name = 'auth'): string;

    public function deleteCurrentToken(User $user): void;

    public function deleteAllTokens(User $user): void;


    /**
     * Check if a user exists by email or phone
     */
    public function existsByIdentifier(string $identifier): bool;
}