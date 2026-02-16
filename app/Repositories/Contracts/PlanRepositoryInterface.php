<?php

namespace App\Repositories\Contracts;

use App\Models\Plan;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface PlanRepositoryInterface
{
    /**
     * Paginate plans with relations
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    /**
     * Get all active plans
     */
    public function allActive(): Collection;

    /**
     * Find plan by id
     */
    public function find(int $id): Plan;

    /**
     * Find plan with relations
     */
    public function findWithRelations(int $id, array $relations = []): Plan;
    /**
     * Get all plans with optional relations
     */
    public function allWithRelations(array $relations = []): Collection;
    /**
     * Create new plan
     */
    public function create(array $data): Plan;

    /**
     * Update plan
     */
    public function update(Plan $plan, array $data): Plan;

    /**
     * Delete plan
     */
    public function delete(Plan $plan): bool;

    /**
     * Update plan features (JSON)
     */
    public function updateFeatures(Plan $plan, array $features): Plan;

    /**
     * Toggle plan active status
     */
    public function toggleStatus(Plan $plan): Plan;
}
