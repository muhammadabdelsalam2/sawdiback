<?php

namespace App\Services\Livestock;

use App\Models\AnimalWeightLog;
use App\Models\LivestockAnimal;

class RecordWeightService
{
    public function execute(array $data): AnimalWeightLog
    {
        $tenantId = $data['tenant_id'] ?? (string) auth()->user()->tenant_id;

        $animal = LivestockAnimal::query()
            ->where('tenant_id', $tenantId)
            ->findOrFail($data['animal_id']);

        return AnimalWeightLog::query()->create([
            'tenant_id'   => $tenantId,
            'animal_id'   => $animal->id,
            'recorded_at' => $data['recorded_at'],
            'weight'      => (float) $data['weight'],
            'notes'       => $data['notes'] ?? null,
        ]);
    }
}