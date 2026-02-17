<?php

namespace App\Services\Livestock;

use App\Models\AnimalVaccination;
use App\Models\LivestockAnimal;
use App\Models\ReproductionCycle;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class LivestockAlertService
{
    public function vaccinationsDueInDays(int $days): Collection
    {
        $toDate = Carbon::today()->addDays(max($days, 0))->toDateString();

        return AnimalVaccination::query()
            ->with(['animal', 'vaccine'])
            ->whereNotNull('next_due_date')
            ->whereBetween('next_due_date', [Carbon::today()->toDateString(), $toDate])
            ->orderBy('next_due_date')
            ->get();
    }

    public function vaccinationsOverdue(): Collection
    {
        return AnimalVaccination::query()
            ->with(['animal', 'vaccine'])
            ->whereNotNull('next_due_date')
            ->whereDate('next_due_date', '<', Carbon::today()->toDateString())
            ->orderBy('next_due_date')
            ->get();
    }

    public function expectedDeliveriesInMonth(?string $month = null): Collection
    {
        $date = $month ? Carbon::parse($month . '-01') : Carbon::today();

        return ReproductionCycle::query()
            ->with(['femaleAnimal', 'maleAnimal'])
            ->where('status', 'pregnant')
            ->whereYear('expected_delivery_date', $date->year)
            ->whereMonth('expected_delivery_date', $date->month)
            ->orderBy('expected_delivery_date')
            ->get();
    }

    public function animalsUnderTreatment(): Collection
    {
        return LivestockAnimal::query()
            ->with(['species', 'breed'])
            ->where('health_status', 'under_treatment')
            ->orderByDesc('updated_at')
            ->get();
    }
}
