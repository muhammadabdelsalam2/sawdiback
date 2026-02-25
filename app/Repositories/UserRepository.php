<?php
namespace App\Repositories;

use App\DTOs\Auth\LoginDTO;
use App\Models\User;
use Illuminate\Support\Collection;

class UserRepository
{
    protected User $model;

    public function __construct(User $user)
    {
        $this->model = $user;
    }

    public function allByTenant(string $tenantId): Collection
    {
        return $this->model->where('tenant_id', $tenantId)->orderBy('created_at', 'desc')->get();
    }

    public function find(string $id): ?User
    {
        return $this->model->find($id);
    }

    public function create(array $data): User
    {
        return $this->model->create($data);
    }

    public function update(User $user, array $data): bool
    {
        return $user->update($data);
    }

    public function delete(User $user): bool
    {
        return $user->delete();
    }
    public function findByIdentifier(LoginDTO $dto): ?User
    {
        return User::query()
            ->when(
                $dto->isEmail(),
                fn($q) => $q->where('email', $dto->identifier)
            )
            ->when(
                $dto->isPhone(),
                fn($q) => $q->where('phone', $dto->identifier)
            )
            ->first();
    }


    public function findById(int $id): ?User
    {
        return User::find($id);
    }

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function findByPhone(string $phone): ?User
    {
        return User::where('phone', $phone)->first();
    }



    public function findByProvider(string $provider, string $providerId): ?User
    {
        return User::where('provider', $provider)
            ->where('provider_id', $providerId)
            ->first();
    }

    public function existsByEmail(string $email): bool
    {
        return User::where('email', $email)->exists();
    }

    public function existsByPhone(string $phone): bool
    {
        return User::where('phone', $phone)->exists();
    }



    public function updateById(int $id, array $data): bool
    {
        return User::where('id', $id)->update($data);
    }



    public function deleteById(int $id): bool
    {
        return User::where('id', $id)->delete();
    }

    public function getAll(): Collection
    {
        return User::all();
    }

    public function paginate(int $perPage = 15)
    {
        return User::paginate($perPage);
    }

    public function assignRole(User $user, string $role): void
    {
        $user->assignRole($role);
    }

    public function syncRoles(User $user, array $roles): void
    {
        $user->syncRoles($roles);
    }

    public function hasRole(User $user, string $role): bool
    {
        return $user->hasRole($role);
    }

    public function updateProvider(
        User $user,
        string $provider,
        string $providerId,
        ?string $avatar = null
    ): bool {
        return $user->update([
            'provider' => $provider,
            'provider_id' => $providerId,
            'avatar' => $avatar
        ]);
    }

    public function createToken(User $user, string $name = 'auth'): string
    {
        return $user->createToken($name)->plainTextToken;
    }

    public function deleteCurrentToken(User $user): void
    {
        $user->currentAccessToken()->delete();
    }

    public function deleteAllTokens(User $user): void
    {
        $user->tokens()->delete();
    }

 



    public function existsByIdentifier(string $identifier): bool
    {
        return User::where('email', $identifier)
            ->orWhere('phone', $identifier)
            ->exists();
    }
}
