<?php

namespace App\Services\Livestock;

use App\Models\AnimalWeightLog;

class RecordWeightService
{
    public function execute(array $data): AnimalWeightLog
    {
        return AnimalWeightLog::query()->create([
            'tenant_id' => $data['tenant_id'] ?? null,
            'animal_id' => $data['animal_id'],
            'recorded_at' => $data['recorded_at'],
            'weight' => $data['weight'],
            'notes' => $data['notes'] ?? null,
        ]);
    }
}
