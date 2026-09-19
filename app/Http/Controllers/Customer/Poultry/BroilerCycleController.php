<?php

namespace App\Http\Controllers\Customer\Poultry;

use App\Http\Controllers\Controller;
use App\Models\FarmPen;
use App\Models\Poultry\PoultryBroilerCost;
use App\Models\Poultry\PoultryBroilerCycle;
use App\Models\Poultry\PoultryBroilerMortality;
use App\Models\Poultry\PoultryBroilerSale;
use App\Services\Poultry\PoultryFinancialService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BroilerCycleController extends Controller
{
    public function index(string $locale): View
    {
        $tenantId = (string) auth()->user()->tenant_id;

        $cycles = PoultryBroilerCycle::query()
            ->where('tenant_id', $tenantId)
            ->with(['mortalities', 'sales', 'costs'])
            ->orderByDesc('started_at')
            ->paginate(15);

        $currentLocale = $locale;

        return view('dashboard.customer.poultry.broiler_cycles.index', compact('cycles', 'currentLocale'));
    }

    public function create(string $locale): View
    {
        $tenantId = (string) auth()->user()->tenant_id;
        $pens = FarmPen::query()->where('tenant_id', $tenantId)->forSelect()->get();
        $currentLocale = $locale;

        return view('dashboard.customer.poultry.broiler_cycles.create', compact('pens', 'currentLocale'));
    }

    public function store(Request $request, string $locale): RedirectResponse
    {
        $tenantId = (string) auth()->user()->tenant_id;

        $validated = $request->validate([
            'farm_pen_id'      => ['required', 'integer'],
            'cycle_number'     => ['required', 'string', 'max:100'],
            'bird_type'        => ['nullable', 'string', 'max:100'],
            'chick_source'     => ['nullable', 'string', 'max:150'],
            'initial_quantity' => ['required', 'integer', 'min:1'],
            'initial_weight_g' => ['nullable', 'numeric', 'min:0'],
            'started_at'       => ['required', 'date'],
            'target_weight_kg' => ['nullable', 'numeric', 'min:0'],
            'notes'            => ['nullable', 'string'],
        ]);

        $cycle = PoultryBroilerCycle::query()->create([
            'tenant_id' => $tenantId,
            ...$validated,
        ]);

        return redirect()
            ->route('customer.poultry.broiler-cycles.show', ['locale' => $locale, 'broiler_cycle' => $cycle->id])
            ->with('success', __('poultry.messages.success.broiler_cycle_created') ?? 'تم إنشاء دورة التسمين بنجاح');
    }

    public function show(string $locale, PoultryBroilerCycle $broiler_cycle, PoultryFinancialService $financialService): View
    {
        $this->authorizeTenant($broiler_cycle);

        $broiler_cycle->load([
            'mortalities' => fn ($q) => $q->orderByDesc('mortality_date'),
            'sales'       => fn ($q) => $q->orderByDesc('sale_date'),
            'costs'       => fn ($q) => $q->orderByDesc('cost_date'),
        ]);

        $metrics = $financialService->calculateBroilerCycleMetrics($broiler_cycle);
        $currentLocale = $locale;

        return view('dashboard.customer.poultry.broiler_cycles.show', [
            'cycle'         => $broiler_cycle,
            'metrics'       => $metrics,
            'currentLocale' => $currentLocale,
        ]);
    }

    public function edit(string $locale, PoultryBroilerCycle $broiler_cycle): View
    {
        $this->authorizeTenant($broiler_cycle);

        $tenantId = (string) auth()->user()->tenant_id;
        $pens = FarmPen::query()->where('tenant_id', $tenantId)->forSelect()->get();
        $currentLocale = $locale;

        return view('dashboard.customer.poultry.broiler_cycles.edit', [
            'cycle'         => $broiler_cycle,
            'pens'          => $pens,
            'currentLocale' => $currentLocale,
        ]);
    }

    public function update(Request $request, string $locale, PoultryBroilerCycle $broiler_cycle, PoultryFinancialService $financialService): RedirectResponse
    {
        $this->authorizeTenant($broiler_cycle);

        $validated = $request->validate([
            'farm_pen_id'      => ['required', 'integer'],
            'cycle_number'     => ['required', 'string', 'max:100'],
            'status'           => ['nullable', 'string'],
            'target_weight_kg' => ['nullable', 'numeric', 'min:0'],
            'ended_at'         => ['nullable', 'date'],
            'notes'            => ['nullable', 'string'],
        ]);

        $broiler_cycle->update($validated);

        if (in_array($broiler_cycle->status, ['closed', 'completed'], true)) {
            $financialService->recordBroilerCycleJournalEntry($broiler_cycle, auth()->id() ?? 1);
        }

        return redirect()
            ->route('customer.poultry.broiler-cycles.show', ['locale' => $locale, 'broiler_cycle' => $broiler_cycle->id])
            ->with('success', __('poultry.messages.success.broiler_cycle_updated') ?? 'تم تحديث الدورة بنجاح');
    }

    public function destroy(string $locale, PoultryBroilerCycle $broiler_cycle): RedirectResponse
    {
        $this->authorizeTenant($broiler_cycle);

        $broiler_cycle->delete();

        return redirect()
            ->route('customer.poultry.broiler-cycles.index', ['locale' => $locale])
            ->with('success', __('poultry.messages.success.broiler_cycle_deleted') ?? 'تم حذف الدورة بنجاح');
    }

    public function storeMortality(Request $request, string $locale, PoultryBroilerCycle $broiler_cycle): RedirectResponse
    {
        $this->authorizeTenant($broiler_cycle);

        $validated = $request->validate([
            'mortality_date' => ['required', 'date'],
            'quantity'       => ['required', 'integer', 'min:1'],
            'notes'          => ['nullable', 'string', 'max:255'],
        ]);

        PoultryBroilerMortality::query()->create([
            'tenant_id'        => $broiler_cycle->tenant_id,
            'broiler_cycle_id' => $broiler_cycle->id,
            ...$validated,
        ]);

        return redirect()->back()->with('success', __('poultry.messages.success.mortality_recorded') ?? 'تم تسجيل النفوق بنجاح');
    }

    public function storeSale(Request $request, string $locale, PoultryBroilerCycle $broiler_cycle): RedirectResponse
    {
        $this->authorizeTenant($broiler_cycle);

        $validated = $request->validate([
            'sale_date'     => ['required', 'date'],
            'quantity'      => ['required', 'integer', 'min:1'],
            'weight_kg'     => ['nullable', 'numeric', 'min:0'],
            'unit_price'    => ['required', 'numeric', 'min:0'],
            'customer_name' => ['nullable', 'string', 'max:200'],
        ]);

        $validated['tenant_id'] = $broiler_cycle->tenant_id;
        $validated['total_amount'] = round((float) $validated['quantity'] * (float) $validated['unit_price'], 2);

        PoultryBroilerSale::query()->create([
            'broiler_cycle_id' => $broiler_cycle->id,
            ...$validated,
        ]);

        return redirect()->back()->with('success', __('poultry.messages.success.sale_recorded') ?? 'تم تسجيل المبيعات بنجاح');
    }

    public function storeCost(Request $request, string $locale, PoultryBroilerCycle $broiler_cycle): RedirectResponse
    {
        $this->authorizeTenant($broiler_cycle);

        $validated = $request->validate([
            'cost_type'   => ['required', 'string', 'in:feed,electricity,water,fuel,transport,chicks_purchase,slaughter_packaging,other'],
            'amount'      => ['required', 'numeric', 'min:0.01'],
            'quantity_kg' => ['nullable', 'numeric', 'min:0'],
            'cost_date'   => ['required', 'date'],
            'notes'       => ['nullable', 'string', 'max:255'],
        ]);

        PoultryBroilerCost::query()->create([
            'tenant_id'        => $broiler_cycle->tenant_id,
            'broiler_cycle_id' => $broiler_cycle->id,
            ...$validated,
        ]);

        return redirect()->back()->with('success', __('poultry.messages.success.cost_recorded') ?? 'تم تسجيل التكلفة بنجاح');
    }

    private function authorizeTenant(PoultryBroilerCycle $cycle): void
    {
        $tenantId = (string) auth()->user()->tenant_id;
        if ((string) $cycle->tenant_id !== $tenantId) {
            abort(403);
        }
    }
}
