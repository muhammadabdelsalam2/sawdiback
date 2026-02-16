<?php

namespace App\Services\Livestock;

use App\Models\AnimalHealthRecord;
use App\Models\LivestockAnimal;
use Illuminate\Support\Facades\DB;

class RecordHealthService
{
    public function execute(array $data): AnimalHealthRecord
    {
        return DB::transaction(function () use ($data) {
            $record = AnimalHealthRecord::query()->create([
                'tenant_id' => $data['tenant_id'] ?? null,
                'animal_id' => $data['animal_id'],
                'record_type' => $data['record_type'],
                'diagnosis' => $data['diagnosis'],
                'treatment' => $data['treatment'],
                'vet_employee_id' => $data['vet_employee_id'] ?? null,
                'cost' => $data['cost'] ?? null,
                'next_followup_date' => $data['next_followup_date'] ?? null,
            ]);

            if (!empty($data['set_animal_under_treatment'])) {
                LivestockAnimal::query()
                    ->whereKey($data['animal_id'])
                    ->update(['health_status' => 'under_treatment']);
            }

            return $record->fresh(['animal']);
        });
    }
}
