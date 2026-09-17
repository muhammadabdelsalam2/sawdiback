<?php

namespace App\Http\Controllers\Customer\Poultry;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\Poultry\ChickenBreedEggLogStoreRequest;
use App\Http\Requests\Customer\Poultry\ChickenBreedStoreRequest;
use App\Http\Requests\Customer\Poultry\ChickenBreedUpdateRequest;
use App\Models\FarmPen;
use App\Models\Poultry\PoultryChickenBreed;
use App\Models\Poultry\PoultryChickenBreedEggLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class ChickenBreedController extends Controller
{
    public function index(string $locale): View
    {
        $tenantId = (string) auth()->user()->tenant_id;

        $breeds = PoultryChickenBreed::query()
            ->when(Schema::hasColumn('poultry_chicken_breeds', 'tenant_id'), fn ($q) => $q->where('tenant_id', $tenantId))
            ->with('pen.farm')
            ->withCount('eggLogs')
            ->orderBy('code')
            ->paginate(15);

        return view('dashboard.customer.poultry.chicken_breeds.index', [
            'breeds'        => $breeds,
            'currentLocale' => $locale,
        ]);
    }

    public function create(string $locale): View
    {
        $tenantId = (string) auth()->user()->tenant_id;

        $pens = FarmPen::query()
            ->when(Schema::hasColumn('farm_pens', 'tenant_id'), fn ($q) => $q->where('tenant_id', $tenantId))
            ->with('farm')
            ->forSelect()
            ->get();

        return view('dashboard.customer.poultry.chicken_breeds.create', [
            'pens'          => $pens,
            'currentLocale' => $locale,
        ]);
    }

    public function store(ChickenBreedStoreRequest $request, string $locale): RedirectResponse
    {
        $data = $request->validated();
        if (Schema::hasColumn('poultry_chicken_breeds', 'tenant_id')) {
            $data['tenant_id'] = (string) auth()->user()->tenant_id;
        }

        $breed = PoultryChickenBreed::query()->create($data);

        return redirect()
            ->route('customer.poultry.chicken-breeds.show', ['locale' => $locale, 'chicken_breed' => $breed->id])
            ->with('success', __('poultry.messages.success.chicken_breed_created') ?? 'تم تسجيل السلالة بنجاح.');
    }

    public function show(string $locale, PoultryChickenBreed $chicken_breed): View
    {
        $tenantId = (string) auth()->user()->tenant_id;
        if (Schema::hasColumn('poultry_chicken_breeds', 'tenant_id') && (string) $chicken_breed->tenant_id !== $tenantId) {
            abort(403);
        }

        $chicken_breed->load([
            'pen.farm',
            'eggLogs' => fn ($q) => $q->orderByDesc('production_date'),
        ]);

        $totalEggs = (int) $chicken_breed->eggLogs()->sum('eggs_count');
        $femaleCount = (int) ($chicken_breed->female_count ?? 0);
        $avgEggsPerHen = $femaleCount > 0 ? round($totalEggs / $femaleCount, 1) : 0;

        $metrics = [
            'total_eggs'        => $totalEggs,
            'female_count'      => $femaleCount,
            'male_count'        => (int) ($chicken_breed->male_count ?? 0),
            'total_birds'       => $femaleCount + (int) ($chicken_breed->male_count ?? 0),
            'avg_eggs_per_hen'  => $avgEggsPerHen,
            'total_trays'       => round($totalEggs / 30, 1),
        ];

        return view('dashboard.customer.poultry.chicken_breeds.show', [
            'breed'         => $chicken_breed,
            'metrics'       => $metrics,
            'currentLocale' => $locale,
        ]);
    }

    public function edit(string $locale, PoultryChickenBreed $chicken_breed): View
    {
        $tenantId = (string) auth()->user()->tenant_id;
        if (Schema::hasColumn('poultry_chicken_breeds', 'tenant_id') && (string) $chicken_breed->tenant_id !== $tenantId) {
            abort(403);
        }

        $pens = FarmPen::query()
            ->when(Schema::hasColumn('farm_pens', 'tenant_id'), fn ($q) => $q->where('tenant_id', $tenantId))
            ->with('farm')
            ->forSelect()
            ->get();

        return view('dashboard.customer.poultry.chicken_breeds.edit', [
            'breed'         => $chicken_breed,
            'pens'          => $pens,
            'currentLocale' => $locale,
        ]);
    }

    public function update(ChickenBreedUpdateRequest $request, string $locale, PoultryChickenBreed $chicken_breed): RedirectResponse
    {
        $tenantId = (string) auth()->user()->tenant_id;
        if (Schema::hasColumn('poultry_chicken_breeds', 'tenant_id') && (string) $chicken_breed->tenant_id !== $tenantId) {
            abort(403);
        }

        $chicken_breed->update($request->validated());

        return redirect()
            ->route('customer.poultry.chicken-breeds.show', ['locale' => $locale, 'chicken_breed' => $chicken_breed->id])
            ->with('success', __('poultry.messages.success.chicken_breed_updated') ?? 'تم تحديث بيانات السلالة بنجاح.');
    }

    public function destroy(string $locale, PoultryChickenBreed $chicken_breed): RedirectResponse
    {
        $tenantId = (string) auth()->user()->tenant_id;
        if (Schema::hasColumn('poultry_chicken_breeds', 'tenant_id') && (string) $chicken_breed->tenant_id !== $tenantId) {
            abort(403);
        }

        $chicken_breed->delete();

        return redirect()
            ->route('customer.poultry.chicken-breeds.index', ['locale' => $locale])
            ->with('success', __('poultry.messages.success.chicken_breed_deleted') ?? 'تم حذف السلالة بنجاح.');
    }

    public function storeEggLog(ChickenBreedEggLogStoreRequest $request, string $locale, PoultryChickenBreed $chicken_breed): RedirectResponse
    {
        $tenantId = (string) auth()->user()->tenant_id;
        if (Schema::hasColumn('poultry_chicken_breeds', 'tenant_id') && (string) $chicken_breed->tenant_id !== $tenantId) {
            abort(403);
        }

        $data = $request->validated();
        if (Schema::hasColumn('poultry_chicken_breed_egg_logs', 'tenant_id')) {
            $data['tenant_id'] = $tenantId;
        }

        PoultryChickenBreedEggLog::query()->create([
            'chicken_breed_id' => $chicken_breed->id,
            ...$data,
        ]);

        return redirect()->back()->with('success', __('poultry.messages.success.egg_log_recorded') ?? 'تم تسجيل جمع البيض بنجاح.');
    }
}