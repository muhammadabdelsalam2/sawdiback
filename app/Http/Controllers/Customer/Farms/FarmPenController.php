<?php

namespace App\Http\Controllers\Customer\Farms;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\Farms\FarmPenStoreRequest;
use App\Http\Requests\Customer\Farms\FarmPenUpdateRequest;
use App\Http\Requests\Customer\Farms\LivestockPenFinancialEntryStoreRequest;
use App\Models\Farm;
use App\Models\FarmPen;
use App\Models\LivestockPenFinancialEntry;
use App\Services\Livestock\LivestockPenProfitService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FarmPenController extends Controller
{
    public function index(): View
    {
        $pens = FarmPen::query()->with(['farm', 'animals'])->orderBy('pen_number')->paginate(15);

        return view('dashboard.customer.farms.pens.index', compact('pens'));
    }

    public function create(): View
    {
        $farms = Farm::query()->orderBy('name')->get();

        return view('dashboard.customer.farms.pens.create', compact('farms'));
    }

    public function store(FarmPenStoreRequest $request, string $locale): RedirectResponse
    {
        $pen = FarmPen::query()->create($request->validated());

        return redirect()->route('customer.farm-pens.show', ['locale' => $locale, 'farm_pen' => $pen->id])
            ->with('success', __('farms.messages.success.pen_created'));
    }

    public function show(string $locale, FarmPen $farm_pen, LivestockPenProfitService $profitService): View
    {
        $farm_pen->load([
            'farm',
            'animals.species',
            'animals.breed',
            'financialEntries' => fn ($q) => $q->orderByDesc('entry_date'),
        ]);

        return view('dashboard.customer.farms.pens.show', [
            'pen' => $farm_pen,
            'profitSummary' => $profitService->summary($farm_pen),
        ]);
    }

    public function storeFinancialEntry(
        LivestockPenFinancialEntryStoreRequest $request,
        string $locale,
        FarmPen $farm_pen
    ): RedirectResponse {
        LivestockPenFinancialEntry::query()->create([
            'tenant_id' => $farm_pen->tenant_id,
            'pen_id' => $farm_pen->id,
            ...$request->validated(),
        ]);

        return redirect()
            ->route('customer.farm-pens.show', ['locale' => $locale, 'farm_pen' => $farm_pen->id])
            ->with('success', __('farms.messages.success.financial_entry_recorded'));
    }

    public function edit(string $locale, FarmPen $farm_pen): View
    {
        $farms = Farm::query()->orderBy('name')->get();

        return view('dashboard.customer.farms.pens.edit', ['pen' => $farm_pen, 'farms' => $farms]);
    }

    public function update(FarmPenUpdateRequest $request, string $locale, FarmPen $farm_pen): RedirectResponse
    {
        $farm_pen->update($request->validated());

        return redirect()->route('customer.farm-pens.show', ['locale' => $locale, 'farm_pen' => $farm_pen->id])
            ->with('success', __('farms.messages.success.pen_updated'));
    }

    public function destroy(string $locale, FarmPen $farm_pen): RedirectResponse
    {
        $farm_pen->delete();

        return redirect()->route('customer.farm-pens.index', ['locale' => $locale])
            ->with('success', __('farms.messages.success.pen_deleted'));
    }
}
