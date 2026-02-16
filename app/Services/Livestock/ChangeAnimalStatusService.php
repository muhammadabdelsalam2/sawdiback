<?php

namespace App\Services\Livestock;

use App\Models\LivestockAnimal;
use Illuminate\Support\Facades\DB;

class ChangeAnimalStatusService
{
    public function execute(LivestockAnimal $animal, array $data): LivestockAnimal
    {
        DB::transaction(function () use ($animal, $data) {
            $animal->update([
                'status' => $data['status'],
                'notes' => $data['notes'] ?? $animal->notes,
            ]);
        });

        return $animal->fresh();
    }
}
