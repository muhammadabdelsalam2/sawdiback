<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface CustomerRepositoryInterface
{
    public function create(array $data): User;

    public function update(int $id, array $data): User;

    public function delete(int $id): bool;

    public function find(int $id): ?User;

    public function findByEmail(string $email): ?User;

    public function getAll(): Collection;

    public function paginate(int $perPage = 15): LengthAwarePaginator;
}