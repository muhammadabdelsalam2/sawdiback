<?php

namespace App\Http\Controllers\Customer\Poultry;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\Poultry\LayerEggProductionLogStoreRequest;
use App\Http\Requests\Customer\Poultry\LayerFlockStoreRequest;
use App\Http\Requests\Customer\Poultry\LayerFlockUpdateRequest;
use App\Http\Requests\Customer\Poultry\LayerMortalityStoreRequest;
use App\Models\FarmPen;
use App\Models\Poultry\PoultryLayerEggProductionLog;
use App\Models\Poultry\PoultryLayerFlock;
use App\Models\Poultry\PoultryLayerMortality;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LayerFlockController extends Controller
{
    public function index(): View
    {
        $flocks = PoultryLayerFlock::query()
            ->with(['eggProductionLogs', 'mortalities'])
            ->orderByDesc('started_at')
            ->paginate(15);

        return view('dashboard.customer.poultry.layer_flocks.index', compact('flocks'));
    }

    public function create(): View
    {
        $pens = FarmPen::query()->forSelect()->get();

        return view('dashboard.customer.poultry.layer_flocks.create', compact('pens'));
    }

    public function store(LayerFlockStoreRequest $request, string $locale): RedirectResponse
    {
        $flock = PoultryLayerFlock::query()->create($request->validated());

        return redirect()
            ->route('customer.poultry.layer-flocks.show', ['locale' => $locale, 'layer_flock' => $flock->id])
            ->with('success', __('poultry.messages.success.layer_flock_created'));
    }

    public function show(string $locale, PoultryLayerFlock $layer_flock): View
    {
        $layer_flock->load([
            'eggProductionLogs' => fn ($q) => $q->orderByDesc('production_date'),
            'mortalities' => fn ($q) => $q->orderByDesc('mortality_date'),
        ]);

        return view('dashboard.customer.poultry.layer_flocks.show', ['flock' => $layer_flock]);
    }

    public function edit(string $locale, PoultryLayerFlock $layer_flock): View
    {
        $pens = FarmPen::query()->forSelect()->get();

        return view('dashboard.customer.poultry.layer_flocks.edit', ['flock' => $layer_flock, 'pens' => $pens]);
    }

    public function update(LayerFlockUpdateRequest $request, string $locale, PoultryLayerFlock $layer_flock): RedirectResponse
    {
        $layer_flock->update($request->validated());

        return redirect()
            ->route('customer.poultry.layer-flocks.show', ['locale' => $locale, 'layer_flock' => $layer_flock->id])
            ->with('success', __('poultry.messages.success.layer_flock_updated'));
    }

    public function destroy(string $locale, PoultryLayerFlock $layer_flock): RedirectResponse
    {
        $layer_flock->delete();

        return redirect()->route('customer.poultry.layer-flocks.index', ['locale' => $locale])
            ->with('success', __('poultry.messages.success.layer_flock_deleted'));
    }

    public function storeEggLog(LayerEggProductionLogStoreRequest $request, string $locale, PoultryLayerFlock $layer_flock): RedirectResponse
    {
        PoultryLayerEggProductionLog::query()->create([
            'layer_flock_id' => $layer_flock->id,
            ...$request->validated(),
        ]);

        return redirect()->back()->with('success', __('poultry.messages.success.egg_log_recorded'));
    }

    public function storeMortality(LayerMortalityStoreRequest $request, string $locale, PoultryLayerFlock $layer_flock): RedirectResponse
    {
        PoultryLayerMortality::query()->create([
            'layer_flock_id' => $layer_flock->id,
            ...$request->validated(),
        ]);

        return redirect()->back()->with('success', __('poultry.messages.success.mortality_recorded'));
    }
    /**
     * حساب مؤشرات قطيع البياض (إنتاج البيض السليم/الكسر ومعدل إنتاج البياض اليومي Hen-Day %)
     */
    public function calculateLayerFlockMetrics($flock): array
    {
        $initialBirds = (int) ($flock->initial_hens_count ?? $flock->birds_count ?? $flock->initial_count ?? 0);

        // عمود النفوق
        $mortalityCol = Schema::hasColumn('poultry_layer_mortalities', 'quantity') ? 'quantity' : 
                       (Schema::hasColumn('poultry_layer_mortalities', 'count') ? 'count' : 'mortality_count');
        $totalMortality = (int) ($flock->mortalities()->sum($mortalityCol) ?? 0);

        $currentLiveHens = max(0, $initialBirds - $totalMortality);
        $mortalityRate = $initialBirds > 0 ? round(($totalMortality / $initialBirds) * 100, 2) : 0;

        // تتبع إنتاج البيض
        $goodEggsCol = Schema::hasColumn('poultry_layer_egg_production_logs', 'good_eggs_count') ? 'good_eggs_count' : 
                      (Schema::hasColumn('poultry_layer_egg_production_logs', 'good_eggs') ? 'good_eggs' : 'total_eggs');
        $damagedEggsCol = Schema::hasColumn('poultry_layer_egg_production_logs', 'damaged_eggs_count') ? 'damaged_eggs_count' : 
                         (Schema::hasColumn('poultry_layer_egg_production_logs', 'damaged_eggs') ? 'damaged_eggs' : 'broken_eggs');

        $totalGoodEggs = (int) ($flock->eggProductionLogs()->sum($goodEggsCol) ?? 0);
        $totalDamagedEggs = Schema::hasColumn('poultry_layer_egg_production_logs', $damagedEggsCol)
            ? (int) ($flock->eggProductionLogs()->sum($damagedEggsCol) ?? 0)
            : 0;
        $totalEggsProduced = $totalGoodEggs + $totalDamagedEggs;

        // حساب معدل Hen-Day % لأحدث يوم إنتاج
        $latestLog = $flock->eggProductionLogs()->orderByDesc('production_date')->first();
        $latestDayEggs = $latestLog ? (int) ($latestLog->{$goodEggsCol} ?? 0) : 0;
        $henDayProductionRate = $currentLiveHens > 0 ? round(($latestDayEggs / $currentLiveHens) * 100, 2) : 0;

        // إجمالي عدد أطباق/كراتين البيض (الطبق = 30 بيضة)
        $totalTrays = round($totalGoodEggs / 30, 1);

        return [
            'initial_birds'            => $initialBirds,
            'current_live_hens'        => $currentLiveHens,
            'total_mortality'          => $totalMortality,
            'mortality_rate'           => $mortalityRate,
            'total_good_eggs'          => $totalGoodEggs,
            'total_damaged_eggs'       => $totalDamagedEggs,
            'total_eggs_produced'      => $totalEggsProduced,
            'total_trays'              => $totalTrays,
            'latest_day_eggs'          => $latestDayEggs,
            'hen_day_production_rate'  => $henDayProductionRate,
        ];
    }

    /**
     * ترحيل القيد المحاسبي لإغلاق/تسوية قطيع البياض
     */
    public function recordLayerFlockJournalEntry($flock, int $userId): void
    {
        $metrics = $this->calculateLayerFlockMetrics($flock);

        DB::transaction(function () use ($flock, $metrics, $userId) {
            $journalEntry = \App\Models\Customer\Finance\JournalEntry::create([
                'entry_number' => 'JV-LF-' . $flock->id . '-' . time(),
                'entry_date'   => now(),
                'description'  => 'تسوية إنتاج قطيع البياض رقم #' . $flock->id,
                'created_by'   => $userId,
                'status'       => 'posted',
            ]);

            // قيد وسيط لمخزون البيض الناتج (مدين مخزون بيض المائدة / دائن إيرادات تشغيل بياض)
            if ($metrics['total_good_eggs'] > 0) {
                $journalEntry->lines()->create([
                    'account_id'  => 7, // حساب مخزون البيض
                    'debit'       => $metrics['total_good_eggs'],
                    'credit'      => 0,
                    'description' => 'إجمالي عدد البيض السليم لقطيع #' . $flock->id,
                ]);
                $journalEntry->lines()->create([
                    'account_id'  => 8, // حساب إنتاجية قطعان البياض
                    'debit'       => 0,
                    'credit'      => $metrics['total_good_eggs'],
                    'description' => 'إثبات إنتاج بيض لقطيع #' . $flock->id,
                ]);
            }
        });
    }
}