<?php
namespace App\Repositories;

use App\Models\Tenant;
use Illuminate\Support\Collection;

class TenantRepository
{
    protected Tenant $model;

    public function __construct(Tenant $tenant)
    {
        $this->model = $tenant;
    }

    public function all(): Collection
    {
        return $this->model->orderBy('created_at', 'desc')->get();
    }

    public function find(string $id): ?Tenant
    {
        return $this->model->where('id', $id)->first();
    }

    public function create(array $data): Tenant
    {
        return $this->model->create($data);
    }

    public function update(Tenant $tenant, array $data): bool
    {
        return $tenant->update($data);
    }

    public function delete(Tenant $tenant): bool
    {
        return $tenant->delete();
    }

    public function withPlan(string $id): ?Tenant
    {
        return $this->model->with('plan')->find($id);
    }
}
