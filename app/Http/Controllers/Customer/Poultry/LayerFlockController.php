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
}
