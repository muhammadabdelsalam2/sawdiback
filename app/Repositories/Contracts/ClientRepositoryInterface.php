<?php

namespace App\Repositories\Contracts;

use App\Models\User;

interface ClientRepositoryInterface
{
    public function findById(int $id): ?User;

    // Create a new client
    public function create(array $data): User;
    
    public function update(User $user, array $data): User;
}