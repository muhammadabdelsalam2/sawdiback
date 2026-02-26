<?php

namespace App\Repositories\Contracts;
use App\Models\User;
interface TenantRepositoryInterface
{
    //

    public function all(): \Illuminate\Support\Collection;
    public function find(string $id): ?\App\Models\Tenant;
    public function create(array $data): \App\Models\Tenant;
    public function update(\App\Models\Tenant $tenant, array $data): bool;
    public function delete(\App\Models\Tenant $tenant): bool;
    public function withPlan(string $id): ?\App\Models\Tenant;
    public function createTenantForUser(User $user): void;
}
