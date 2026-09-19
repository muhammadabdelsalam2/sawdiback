<?php

namespace App\Services\Livestock;

use App\Models\AnimalStatusHistory;
use App\Models\FarmPen;
use App\Models\LivestockAnimal;
use Illuminate\Support\Facades\DB;

class ChangeAnimalStatusService
{
    public function execute(LivestockAnimal $animal, array $data): LivestockAnimal
    {
        return DB::transaction(function () use ($animal, $data) {
            $oldStatus = $animal->status;
            $newStatus = $data['status'];
            $reason    = $data['reason'] ?? ($data['notes'] ?? null);

            // 1. إذا لم تتغير الحالة، نكتفي بتحديث الملاحظات إن وجدت
            if ($oldStatus === $newStatus) {
                if (isset($data['notes'])) {
                    $animal->update(['notes' => $data['notes']]);
                }
                return $animal->fresh();
            }

            // 2. تحديث حالة الحيوان
            $animal->update([
                'status' => $newStatus,
                'notes'  => $data['notes'] ?? $animal->notes,
            ]);

            // 3. تسجيل الحدث في جدول تاريخ الحالات
            AnimalStatusHistory::query()->create([
                'tenant_id'     => $animal->tenant_id,
                'animal_id'     => $animal->id,
                'old_status'    => $oldStatus,
                'new_status'    => $newStatus,
                'change_reason' => $reason,
                'changed_at'    => now(),
            ]);

            // 4. مزامنة عداد الحظيرة الحالية بدقة
            if ($animal->pen_id) {
                // الخروج من النشاط (بيع، نفوق، ذبح...)
                if ($oldStatus === 'active' && $newStatus !== 'active') {
                    FarmPen::query()
                        ->where('tenant_id', $animal->tenant_id)
                        ->where('id', $animal->pen_id)
                        ->where('current_count', '>', 0)
                        ->decrement('current_count');
                }
                // العودة إلى الحالة النشطة
                elseif ($oldStatus !== 'active' && $newStatus === 'active') {
                    FarmPen::query()
                        ->where('tenant_id', $animal->tenant_id)
                        ->where('id', $animal->pen_id)
                        ->increment('current_count');
                }
            }

            return $animal->fresh(['pen', 'statusHistory']);
        });
    }
}