<?php

namespace App\Services\Livestock;

use App\Models\AnimalVaccination;
use App\Models\LivestockAnimal;
use App\Models\LivestockPenFinancialEntry;
use App\Models\Vaccine;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RecordVaccinationService
{
    public function execute(array $data): AnimalVaccination
    {
        return DB::transaction(function () use ($data) {
            $tenantId = $data['tenant_id'] ?? (string) auth()->user()->tenant_id;

            $vaccine = Vaccine::query()
                ->where('tenant_id', $tenantId)
                ->findOrFail($data['vaccine_id']);

            $animal = LivestockAnimal::query()
                ->where('tenant_id', $tenantId)
                ->findOrFail($data['animal_id']);

            $vaccinationDate = Carbon::parse($data['vaccination_date']);
            $nextDueDate = $data['next_due_date'] ?? null;

            // حساب تاريخ الجرعة القادمة تلقائياً إذا لم يُحدد يدوياً
            if (!$nextDueDate && $vaccine->default_interval_days) {
                $nextDueDate = $vaccinationDate->copy()->addDays((int) $vaccine->default_interval_days)->toDateString();
            }

            $vaccination = AnimalVaccination::query()->create([
                'tenant_id'                   => $tenantId,
                'animal_id'                   => $animal->id,
                'pen_id'                      => $animal->pen_id,
                'vaccine_id'                  => $vaccine->id,
                'dose_number'                 => $data['dose_number'],
                'vaccination_date'            => $vaccinationDate->toDateString(),
                'next_due_date'               => $nextDueDate,
                'administered_by_employee_id' => $data['administered_by_employee_id'] ?? null,
                'notes'                       => $data['notes'] ?? null,
            ]);

            // قيد مالي في حال كان اللقاح له تكلفة محددة cost_per_unit
            $cost = isset($data['cost']) && $data['cost'] !== null 
                ? (float) $data['cost'] 
                : (float) ($vaccine->cost_per_unit ?? 0);

            if ($animal->pen_id && $cost > 0) {
                LivestockPenFinancialEntry::query()->create([
                    'tenant_id'  => $tenantId,
                    'pen_id'     => $animal->pen_id,
                    'type'       => 'veterinary_costs',
                    'amount'     => $cost,
                    'entry_date' => $vaccinationDate->toDateString(),
                    'notes'      => 'تطعيم الحيوان #' . ($animal->tag_number ?? $animal->id) . ' - ' . $vaccine->name . ' (جرعة ' . $data['dose_number'] . ')',
                ]);
            }

            return $vaccination;
        });
    }
}