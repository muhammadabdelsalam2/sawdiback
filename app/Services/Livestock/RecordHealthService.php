<?php

namespace App\Services\Livestock;

use App\Models\AnimalHealthRecord;
use App\Models\LivestockAnimal;
use App\Models\LivestockPenFinancialEntry;
use Illuminate\Support\Facades\DB;

class RecordHealthService
{
    public function execute(array $data): AnimalHealthRecord
    {
        return DB::transaction(function () use ($data) {
            $tenantId = $data['tenant_id'] ?? (string) auth()->user()->tenant_id;

            $animal = LivestockAnimal::query()
                ->where('tenant_id', $tenantId)
                ->findOrFail($data['animal_id']);

            $cost = isset($data['cost']) && $data['cost'] !== null ? (float) $data['cost'] : null;

            $record = AnimalHealthRecord::query()->create([
                'tenant_id'          => $tenantId,
                'animal_id'          => $animal->id,
                'record_type'        => $data['record_type'],
                'diagnosis'          => $data['diagnosis'],
                'treatment'          => $data['treatment'],
                'vet_employee_id'    => $data['vet_employee_id'] ?? null,
                'cost'               => $cost,
                'next_followup_date' => $data['next_followup_date'] ?? null,
            ]);

            // تحديث الحالة الصحية للحيوان إذا تم تفعيل الخيار
            if (!empty($data['set_animal_under_treatment'])) {
                $animal->update(['health_status' => 'under_treatment']);
            }

            // تسجيل قيد مالي على الحظيرة في حال وجود تكلفة
            if ($animal->pen_id && $cost !== null && $cost > 0) {
                LivestockPenFinancialEntry::query()->create([
                    'tenant_id'  => $tenantId,
                    'pen_id'     => $animal->pen_id,
                    'type'       => 'veterinary_costs',
                    'amount'     => $cost,
                    'entry_date' => now()->toDateString(),
                    'notes'      => 'علاج بيطري للحيوان #' . ($animal->tag_number ?? $animal->id) . ' - ' . $data['diagnosis'],
                ]);
            }

            return $record->fresh(['animal']);
        });
    }
}