<?php

namespace App\Services\Poultry;

use App\Models\Poultry\PoultryBroilerCycle;
use Illuminate\Support\Collection;

class PoultryAlertService
{
    public function highBroilerMortality(float $thresholdPercent = 5.0): Collection
    {
        return PoultryBroilerCycle::query()
            ->with('mortalities')
            ->where('status', 'active')
            ->orderBy('started_at')
            ->get()
            ->filter(fn (PoultryBroilerCycle $cycle) => (float) $cycle->mortality_rate >= $thresholdPercent)
            ->values();
    }

    public function broilerCyclesNearSlaughter(int $expectedAgeDays = 35, int $lookAheadDays = 5): Collection
    {
        $minimumAge = max(0, $expectedAgeDays - $lookAheadDays);

        return PoultryBroilerCycle::query()
            ->where('status', 'active')
            ->whereDate('started_at', '<=', now()->subDays($minimumAge)->toDateString())
            ->orderBy('started_at')
            ->get();
    }
}
