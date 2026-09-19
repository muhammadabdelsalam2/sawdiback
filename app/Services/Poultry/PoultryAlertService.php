<?php

namespace App\Services\Poultry;

use App\Models\Poultry\PoultryLayerFlock;
use Carbon\Carbon;

class PoultryAlertService
{
    /**
     * فحص قطعان البياض التي بلغت عمراً محدداً (الافتراضي 18 أسبوعاً لبدء الإنتاج)
     */
    public function checkLayerFlockAgeAlerts(int $targetAgeWeeks = 18): array
    {
        $alerts = [];
        $activeFlocks = PoultryLayerFlock::query()
            ->whereIn('status', ['active', 'growing'])
            ->get();

        foreach ($activeFlocks as $flock) {
            $startDate = $flock->started_at ?? $flock->hatched_at ?? $flock->created_at;
            if (!$startDate) {
                continue;
            }

            $ageInDays = Carbon::parse($startDate)->diffInDays(now());
            $ageInWeeks = (int) floor($ageInDays / 7);

            if ($ageInWeeks >= $targetAgeWeeks) {
                $alerts[] = [
                    'flock_id'      => $flock->id,
                    'flock_name'    => $flock->name ?? ('قطيع #' . $flock->id),
                    'current_age_w' => $ageInWeeks,
                    'current_age_d' => $ageInDays,
                    'target_age_w'  => $targetAgeWeeks,
                    'message'       => "القطيع بلغ عمر {$ageInWeeks} أسبوعاً (تاريخ البدء: " . Carbon::parse($startDate)->toDateString() . ")",
                ];
            }
        }

        return $alerts;
    }
}