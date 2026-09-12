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

        DB::transaction(function () use ($animal, $toPen) {
            $fromPenId = $animal->pen_id;

            // تحديث الحظيرة للحيوان
            $animal->update(['pen_id' => $toPen->id]);

            // خصم العدد من الحظيرة السابقة إذا وجدت
            if ($fromPenId) {
                FarmPen::where('id', $fromPenId)
                    ->where('current_count', '>', 0)
                    ->decrement('current_count');
            }

            // زيادة العدد في الحظيرة الجديدة
            $toPen->increment('current_count');
        });
    }

    public function syncPenCount(int|string $penId): void
    {
        $count = LivestockAnimal::where('pen_id', $penId)
            ->where('status', 'active')
            ->count();

        FarmPen::where('id', $penId)->update(['current_count' => $count]);
    }
}