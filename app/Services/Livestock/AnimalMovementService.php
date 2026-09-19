<?php

namespace App\Services\Livestock;

use App\Models\FarmPen;
use App\Models\LivestockAnimal;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class AnimalMovementService
{
    public function transfer(LivestockAnimal $animal, FarmPen $toPen): void
    {
        if ((string) $animal->tenant_id !== (string) $toPen->tenant_id) {
            throw new InvalidArgumentException('Unauthorized pen access across tenants.');
        }

        // إذا كانت الحظيرة الهدف هي نفس الحظيرة الحالية لا داعي لتنفيذ أي تعديل
        if ((int) $animal->pen_id === (int) $toPen->id) {
            return;
        }

        DB::transaction(function () use ($animal, $toPen) {
            $fromPenId = $animal->pen_id;
            $tenantId  = $animal->tenant_id;
            $isActive  = $animal->status === 'active';

            // 1. تحديث الحظيرة التابع لها الحيوان
            $animal->update(['pen_id' => $toPen->id]);

            // 2. تحديث العدادات فقط في حال كان الحيوان نشطاً
            if ($isActive) {
                if ($fromPenId) {
                    FarmPen::where('tenant_id', $tenantId)
                        ->where('id', $fromPenId)
                        ->where('current_count', '>', 0)
                        ->decrement('current_count');
                }

                FarmPen::where('tenant_id', $tenantId)
                    ->where('id', $toPen->id)
                    ->increment('current_count');
            }
        });
    }

    public function syncPenCount(int|string $penId, ?string $tenantId = null): void
    {
        $query = FarmPen::query()->where('id', $penId);

        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        $pen = $query->first();

        if (! $pen) {
            return;
        }

        $actualCount = LivestockAnimal::query()
            ->where('tenant_id', $pen->tenant_id)
            ->where('pen_id', $pen->id)
            ->where('status', 'active')
            ->count();

        $pen->update(['current_count' => $actualCount]);
    }
}