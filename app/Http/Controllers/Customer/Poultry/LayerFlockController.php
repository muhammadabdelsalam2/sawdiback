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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class LayerFlockController extends Controller
{
    /**
     * استرجاع كلاس موديل القيود اليومية الفعلي بالمشروع بأمان
     */
    protected function getJournalEntryClass(): ?string
    {
        $classes = [
            'App\Models\Customer\Finance\JournalEntry',
            'App\Models\Finance\JournalEntry',
            'App\Models\JournalEntry',
        ];

        foreach ($classes as $class) {
            if (class_exists($class)) {
                return $class;
            }
        }

        return null;
    }

    public function index(string $locale): View
    {
        $flocks = PoultryLayerFlock::query()
            ->with(['eggProductionLogs', 'mortalities'])
            ->orderByDesc('started_at')
            ->paginate(15);

        return view('dashboard.customer.poultry.layer_flocks.index', [
            'flocks'        => $flocks,
            'currentLocale' => $locale,
        ]);
    }

    public function create(string $locale): View
    {
        $pens = FarmPen::query()->forSelect()->get();

        return view('dashboard.customer.poultry.layer_flocks.create', [
            'pens'          => $pens,
            'currentLocale' => $locale,
        ]);
    }

    public function store(LayerFlockStoreRequest $request, string $locale): RedirectResponse
    {
        $data = $request->validated();
        if (Schema::hasColumn('poultry_layer_flocks', 'tenant_id')) {
            $data['tenant_id'] = auth()->user()->tenant_id ?? null;
        }

        $flock = PoultryLayerFlock::query()->create($data);

        return redirect()
            ->route('customer.poultry.layer-flocks.show', ['locale' => $locale, 'layer_flock' => $flock->id])
            ->with('success', __('poultry.messages.success.layer_flock_created') ?? 'تم إنشاء قطيع البياض بنجاح.');
    }

    public function show(string $locale, PoultryLayerFlock $layer_flock): View
    {
        $layer_flock->load([
            'eggProductionLogs' => fn ($q) => $q->orderByDesc('production_date'),
            'mortalities'       => fn ($q) => $q->orderByDesc('mortality_date'),
        ]);

        $metrics = $this->calculateLayerFlockMetrics($layer_flock);

        return view('dashboard.customer.poultry.layer_flocks.show', [
            'flock'         => $layer_flock,
            'metrics'       => $metrics,
            'currentLocale' => $locale,
        ]);
    }

    public function edit(string $locale, PoultryLayerFlock $layer_flock): View
    {
        $pens = FarmPen::query()->forSelect()->get();

        return view('dashboard.customer.poultry.layer_flocks.edit', [
            'flock'         => $layer_flock,
            'pens'          => $pens,
            'currentLocale' => $locale,
        ]);
    }

    public function update(LayerFlockUpdateRequest $request, string $locale, PoultryLayerFlock $layer_flock): RedirectResponse
    {
        $layer_flock->update($request->validated());

        if (in_array($layer_flock->status, ['closed', 'completed'], true)) {
            $this->recordLayerFlockJournalEntry($layer_flock, auth()->id() ?? 1);
        }

        return redirect()
            ->route('customer.poultry.layer-flocks.show', ['locale' => $locale, 'layer_flock' => $layer_flock->id])
            ->with('success', __('poultry.messages.success.layer_flock_updated') ?? 'تم تحديث بيانات القطيع بنجاح.');
    }

    public function destroy(string $locale, PoultryLayerFlock $layer_flock): RedirectResponse
    {
        $layer_flock->delete();

        return redirect()
            ->route('customer.poultry.layer-flocks.index', ['locale' => $locale])
            ->with('success', __('poultry.messages.success.layer_flock_deleted') ?? 'تم حذف القطيع بنجاح.');
    }

    public function storeEggLog(LayerEggProductionLogStoreRequest $request, string $locale, PoultryLayerFlock $layer_flock): RedirectResponse
    {
        $data = $request->validated();
        if (Schema::hasColumn('poultry_layer_egg_production_logs', 'tenant_id')) {
            $data['tenant_id'] = auth()->user()->tenant_id ?? null;
        }

        PoultryLayerEggProductionLog::query()->create([
            'layer_flock_id' => $layer_flock->id,
            ...$data,
        ]);

        return redirect()->back()->with('success', __('poultry.messages.success.egg_log_recorded') ?? 'تم تسجيل إنتاج البيض بنجاح.');
    }

    public function storeMortality(LayerMortalityStoreRequest $request, string $locale, PoultryLayerFlock $layer_flock): RedirectResponse
    {
        $data = $request->validated();
        if (Schema::hasColumn('poultry_layer_mortalities', 'tenant_id')) {
            $data['tenant_id'] = auth()->user()->tenant_id ?? null;
        }

        PoultryLayerMortality::query()->create([
            'layer_flock_id' => $layer_flock->id,
            ...$data,
        ]);

        return redirect()->back()->with('success', __('poultry.messages.success.mortality_recorded') ?? 'تم تسجيل النفوق بنجاح.');
    }

    /**
     * حساب مؤشرات قطيع البياض (إنتاج البيض السليم/الكسر ومعدل إنتاج البياض اليومي Hen-Day %)
     */
    public function calculateLayerFlockMetrics(PoultryLayerFlock $flock): array
    {
        $initialBirds = (int) ($flock->initial_hens_count ?? $flock->birds_count ?? $flock->initial_count ?? 0);

        // عمود النفوق
        $mortalityCol = Schema::hasColumn('poultry_layer_mortalities', 'quantity') ? 'quantity' : 
                       (Schema::hasColumn('poultry_layer_mortalities', 'count') ? 'count' : 'mortality_count');
        $totalMortality = (int) ($flock->mortalities()->sum($mortalityCol) ?? 0);

        $currentLiveHens = max(0, $initialBirds - $totalMortality);
        $mortalityRate = $initialBirds > 0 ? round(($totalMortality / $initialBirds) * 100, 2) : 0;

        // تتبع إنتاج البيض
        $eggsCol = Schema::hasColumn('poultry_layer_egg_production_logs', 'eggs_count') ? 'eggs_count' : 
                  (Schema::hasColumn('poultry_layer_egg_production_logs', 'good_eggs_count') ? 'good_eggs_count' : 'good_eggs');

        $damagedEggsCol = Schema::hasColumn('poultry_layer_egg_production_logs', 'damaged_count') ? 'damaged_count' : 
                         (Schema::hasColumn('poultry_layer_egg_production_logs', 'damaged_eggs_count') ? 'damaged_eggs_count' : 'damaged_eggs');

        $totalGoodEggs = (int) ($flock->eggProductionLogs()->sum($eggsCol) ?? 0);
        $totalDamagedEggs = Schema::hasColumn('poultry_layer_egg_production_logs', $damagedEggsCol)
            ? (int) ($flock->eggProductionLogs()->sum($damagedEggsCol) ?? 0)
            : 0;
        $totalEggsProduced = $totalGoodEggs + $totalDamagedEggs;

        // حساب معدل Hen-Day % لأحدث يوم إنتاج
        $latestLog = $flock->eggProductionLogs()->orderByDesc('production_date')->first();
        $latestDayEggs = $latestLog ? (int) ($latestLog->{$eggsCol} ?? 0) : 0;
        $henDayProductionRate = $currentLiveHens > 0 ? round(($latestDayEggs / $currentLiveHens) * 100, 2) : 0;

        // إجمالي عدد أطباق البيض (الطبق = 30 بيضة)
        $totalTrays = round($totalGoodEggs / 30, 1);

        return [
            'initial_birds'           => $initialBirds,
            'current_live_hens'       => $currentLiveHens,
            'total_mortality'         => $totalMortality,
            'mortality_rate'          => $mortalityRate,
            'total_good_eggs'         => $totalGoodEggs,
            'total_damaged_eggs'      => $totalDamagedEggs,
            'total_eggs_produced'     => $totalEggsProduced,
            'total_trays'             => $totalTrays,
            'latest_day_eggs'         => $latestDayEggs,
            'hen_day_production_rate' => $henDayProductionRate,
        ];
    }

    /**
     * ترحيل القيد المحاسبي لإغلاق/تسوية قطيع البياض
     */
    public function recordLayerFlockJournalEntry(PoultryLayerFlock $flock, int $userId): void
    {
        $journalClass = $this->getJournalEntryClass();
        if (! $journalClass) {
            return;
        }

        $metrics = $this->calculateLayerFlockMetrics($flock);

        DB::transaction(function () use ($flock, $metrics, $userId, $journalClass) {
            $entryCode = 'JV-LF-' . $flock->id . '-' . time();
            $entryDate = now();

            $journalEntry = $journalClass::create([
                'tenant_id'    => $flock->tenant_id ?? (auth()->check() ? auth()->user()->tenant_id : null),
                'entry_no'     => $entryCode,
                'entry_number' => $entryCode,
                'entry_date'   => $entryDate,
                'date'         => $entryDate,
                'description'  => 'تسوية إنتاج قطيع البياض رقم #' . $flock->id,
                'created_by'   => $userId,
                'status'       => 'posted',
            ]);

            if ($metrics['total_good_eggs'] > 0 && method_exists($journalEntry, 'lines')) {
                $journalEntry->lines()->create([
                    'account_id'  => 7,
                    'debit'       => $metrics['total_good_eggs'],
                    'credit'      => 0,
                    'description' => 'إجمالي عدد البيض السليم لقطيع #' . $flock->id,
                ]);

                $journalEntry->lines()->create([
                    'account_id'  => 8,
                    'debit'       => 0,
                    'credit'      => $metrics['total_good_eggs'],
                    'description' => 'إثبات إنتاج بيض لقطيع #' . $flock->id,
                ]);
            }
        });
    }
}