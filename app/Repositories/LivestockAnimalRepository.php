<?php

namespace App\Repositories;

use App\Models\LivestockAnimal;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class LivestockAnimalRepository
{
    public function paginateWithRelations(int $perPage = 15): LengthAwarePaginator
    {
        return LivestockAnimal::query()
            ->with(['species', 'breed', 'mother', 'father'])
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function findWithRelations(int $id, array $relations = []): LivestockAnimal
    {
        return LivestockAnimal::query()->with($relations)->findOrFail($id);
    }

    public function create(array $data): LivestockAnimal
    {
        return LivestockAnimal::query()->create($data);
    }

    public function update(LivestockAnimal $animal, array $data): LivestockAnimal
    {
        $animal->update($data);
        return $animal;
    }
}
