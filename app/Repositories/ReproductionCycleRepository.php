<?php

namespace App\Repositories;

use App\Models\ReproductionCycle;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ReproductionCycleRepository
{
    public function paginateWithRelations(int $perPage = 15): LengthAwarePaginator
    {
        return ReproductionCycle::query()
            ->with(['femaleAnimal', 'maleAnimal', 'birth'])
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function findWithRelations(int $id, array $relations = []): ReproductionCycle
    {
        return ReproductionCycle::query()->with($relations)->findOrFail($id);
    }

    public function create(array $data): ReproductionCycle
    {
        return ReproductionCycle::query()->create($data);
    }

    public function update(ReproductionCycle $cycle, array $data): ReproductionCycle
    {
        $cycle->update($data);
        return $cycle;
    }
}
