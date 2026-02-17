<?php

namespace App\Services\Livestock;

use App\Models\AnimalVaccination;
use App\Models\Vaccine;
use Carbon\Carbon;

class RecordVaccinationService
{
    public function execute(array $data): AnimalVaccination
    {
        $vaccine = Vaccine::query()->findOrFail($data['vaccine_id']);
        $vaccinationDate = Carbon::parse($data['vaccination_date']);
        $nextDueDate = $data['next_due_date'] ?? null;

        if (!$nextDueDate && $vaccine->default_interval_days) {
            $nextDueDate = $vaccinationDate->copy()->addDays((int) $vaccine->default_interval_days)->toDateString();
        }

        return AnimalVaccination::query()->create([
            'tenant_id' => $data['tenant_id'] ?? null,
            'animal_id' => $data['animal_id'],
            'vaccine_id' => $vaccine->id,
            'dose_number' => $data['dose_number'],
            'vaccination_date' => $vaccinationDate->toDateString(),
            'next_due_date' => $nextDueDate,
            'administered_by_employee_id' => $data['administered_by_employee_id'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);
    }
}
