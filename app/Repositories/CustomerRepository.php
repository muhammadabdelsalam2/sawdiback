<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class CustomerRepository implements CustomerRepositoryInterface
{
    public function create(array $data): User
    {
        $data['password'] = Hash::make($data['password']);

        $customer = User::query()->create($data);

        // assign role
        $customer->assignRole('Customer');

        return $customer;
    }

    public function update(int $id, array $data): User
    {
        $customer = $this->find($id);

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $customer->update($data);

        return $customer->refresh();
    }

    public function delete(int $id): bool
    {
        return User::query()->where('id', $id)->delete();
    }

    public function find(int $id): ?User
    {
        return User::query()
            ->role('Customer')
            ->find($id);
    }

    public function findByEmail(string $email): ?User
    {
        return User::query()
            ->role('Customer')
            ->where('email', $email)
            ->first();
    }

    public function getAll(): Collection
    {
        return User::query()
            ->role('Customer')
            ->latest()
            ->get();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return User::query()
            ->role('Customer')
            ->latest()
            ->paginate($perPage);
    }
}