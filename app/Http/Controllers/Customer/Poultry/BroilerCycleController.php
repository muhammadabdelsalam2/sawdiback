<?php

namespace App\Http\Controllers\Customer\Poultry;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\Poultry\BroilerCostStoreRequest;
use App\Http\Requests\Customer\Poultry\BroilerCycleStoreRequest;
use App\Http\Requests\Customer\Poultry\BroilerCycleUpdateRequest;
use App\Http\Requests\Customer\Poultry\BroilerMortalityStoreRequest;
use App\Http\Requests\Customer\Poultry\BroilerSaleStoreRequest;
use App\Models\FarmPen;
use App\Models\Poultry\PoultryBroilerCost;
use App\Models\Poultry\PoultryBroilerCycle;
use App\Models\Poultry\PoultryBroilerMortality;
use App\Models\Poultry\PoultryBroilerSale;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BroilerCycleController extends Controller
{
    public function index(): View
    {
        $cycles = PoultryBroilerCycle::query()
            ->with(['mortalities', 'sales', 'costs'])
            ->orderByDesc('started_at')
            ->paginate(15);

        return view('dashboard.customer.poultry.broiler_cycles.index', compact('cycles'));
    }

    public function create(): View
    {
        $pens = FarmPen::query()->with('farm')->whereIn('type', ['poultry', 'mixed'])->orderBy('pen_number')->get();

        return view('dashboard.customer.poultry.broiler_cycles.create', compact('pens'));
    }

    public function store(BroilerCycleStoreRequest $request, string $locale): RedirectResponse
    {
        $cycle = PoultryBroilerCycle::query()->create($request->validated());

        return redirect()
            ->route('customer.poultry.broiler-cycles.show', ['locale' => $locale, 'broiler_cycle' => $cycle->id])
            ->with('success', __('poultry.messages.success.broiler_cycle_created'));
    }

    public function show(string $locale, PoultryBroilerCycle $broiler_cycle): View
    {
        $broiler_cycle->load([
            'mortalities' => fn ($q) => $q->orderByDesc('mortality_date'),
            'sales' => fn ($q) => $q->orderByDesc('sale_date'),
            'costs' => fn ($q) => $q->orderByDesc('cost_date'),
        ]);

        return view('dashboard.customer.poultry.broiler_cycles.show', ['cycle' => $broiler_cycle]);
    }

    public function edit(string $locale, PoultryBroilerCycle $broiler_cycle): View
    {
        $pens = FarmPen::query()->with('farm')->whereIn('type', ['poultry', 'mixed'])->orderBy('pen_number')->get();

        return view('dashboard.customer.poultry.broiler_cycles.edit', ['cycle' => $broiler_cycle, 'pens' => $pens]);
    }

    public function update(BroilerCycleUpdateRequest $request, string $locale, PoultryBroilerCycle $broiler_cycle): RedirectResponse
    {
        $broiler_cycle->update($request->validated());

        return redirect()
            ->route('customer.poultry.broiler-cycles.show', ['locale' => $locale, 'broiler_cycle' => $broiler_cycle->id])
            ->with('success', __('poultry.messages.success.broiler_cycle_updated'));
    }

    public function destroy(string $locale, PoultryBroilerCycle $broiler_cycle): RedirectResponse
    {
        $broiler_cycle->delete();

        return redirect()
            ->route('customer.poultry.broiler-cycles.index', ['locale' => $locale])
            ->with('success', __('poultry.messages.success.broiler_cycle_deleted'));
    }

    public function storeMortality(BroilerMortalityStoreRequest $request, string $locale, PoultryBroilerCycle $broiler_cycle): RedirectResponse
    {
        PoultryBroilerMortality::query()->create([
            'broiler_cycle_id' => $broiler_cycle->id,
            ...$request->validated(),
        ]);

        return redirect()->back()->with('success', __('poultry.messages.success.mortality_recorded'));
    }

    public function storeSale(BroilerSaleStoreRequest $request, string $locale, PoultryBroilerCycle $broiler_cycle): RedirectResponse
    {
        $data = $request->validated();
        $data['total_amount'] = round((float) $data['quantity'] * (float) $data['unit_price'], 2);

        PoultryBroilerSale::query()->create([
            'broiler_cycle_id' => $broiler_cycle->id,
            ...$data,
        ]);

        return redirect()->back()->with('success', __('poultry.messages.success.sale_recorded'));
    }

    public function storeCost(BroilerCostStoreRequest $request, string $locale, PoultryBroilerCycle $broiler_cycle): RedirectResponse
    {
        PoultryBroilerCost::query()->create([
            'broiler_cycle_id' => $broiler_cycle->id,
            ...$request->validated(),
        ]);

        return redirect()->back()->with('success', __('poultry.messages.success.cost_recorded'));
    }
}
