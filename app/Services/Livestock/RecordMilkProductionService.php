<?php

namespace App\Services\Livestock;

use App\Models\LivestockAnimal;
use App\Models\MilkProductionLog;
use Illuminate\Validation\ValidationException;

class RecordMilkProductionService
{
    public function execute(array $data): MilkProductionLog
    {
        $animal = LivestockAnimal::query()->findOrFail($data['animal_id']);

        if ($animal->gender !== 'female') {
            throw ValidationException::withMessages([
                'animal_id' => 'Milk production can only be recorded for female animals.',
            ]);
        }

        return MilkProductionLog::query()->updateOrCreate(
            [
                'tenant_id' => $data['tenant_id'] ?? null,
                'animal_id' => $animal->id,
                'production_date' => $data['production_date'],
            ],
            [
                'tenant_id' => $data['tenant_id'] ?? null,
                'animal_id' => $animal->id,
                'production_date' => $data['production_date'],
                'quantity_liters' => $data['quantity_liters'],
                'fat_percentage' => $data['fat_percentage'] ?? null,
                'quality_grade' => $data['quality_grade'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]
        );
    }
}
