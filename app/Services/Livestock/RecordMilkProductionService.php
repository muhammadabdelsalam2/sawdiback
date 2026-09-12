<?php

namespace App\Services\Livestock;

use App\Models\LivestockAnimal;
use App\Models\MilkProductionLog;
use Illuminate\Validation\ValidationException;

class RecordMilkProductionService
{
    public function execute(array $data): MilkProductionLog
    {
        $tenantId = $data['tenant_id'] ?? (string) auth()->user()->tenant_id;

        $animal = LivestockAnimal::query()
            ->where('tenant_id', $tenantId)
            ->findOrFail($data['animal_id']);

        if ($animal->gender !== 'female') {
            throw ValidationException::withMessages([
                'animal_id' => __('livestock.errors.milk_female_only') ?? 'Milk production can only be recorded for female animals.',
            ]);
        }

        return MilkProductionLog::query()->updateOrCreate(
            [
                'tenant_id'       => $tenantId,
                'animal_id'       => $animal->id,
                'production_date' => $data['production_date'],
            ],
            [
                'quantity_liters' => (float) $data['quantity_liters'],
                'fat_percentage'  => isset($data['fat_percentage']) && $data['fat_percentage'] !== '' ? (float) $data['fat_percentage'] : null,
                'quality_grade'   => $data['quality_grade'] ?? null,
                'notes'           => $data['notes'] ?? null,
            ]
        );
    }
}