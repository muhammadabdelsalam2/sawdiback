<?php

namespace App\Repositories;

use App\Models\Plan;
use App\Repositories\Contracts\PlanRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class PlanRepository implements PlanRepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Plan::query()
            ->with('currency')
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function allActive(): Collection
    {
        return Plan::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }

    public function find(int $id): Plan
    {
        return Plan::findOrFail($id);
    }

    public function findWithRelations(int $id, array $relations = []): Plan
    {
        return Plan::with($relations)->findOrFail($id);
    }

    public function create(array $data): Plan
    {
        return Plan::create($data);
    }

    public function update(Plan $plan, array $data): Plan
    {
        $plan->update($data);
        return $plan;
    }

    public function delete(Plan $plan): bool
    {
        return $plan->delete();
    }

    public function updateFeatures(Plan $plan, array $features): Plan
    {
        $plan->features = $features; // JSON
        $plan->save();

        return $plan;
    }

    public function toggleStatus(Plan $plan): Plan
    {
        $plan->is_active = !$plan->is_active;
        $plan->save();

        return $plan;
    }
    public function allWithRelations(array $relations = []): Collection
    {
        return Plan::with($relations)->get();
    }
    public function findById(int $id): Plan
    {
        return Plan::find($id);
    }
}
