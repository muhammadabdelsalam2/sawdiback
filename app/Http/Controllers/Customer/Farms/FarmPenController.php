<?php

namespace App\Http\Controllers\Customer\Farms;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\Farms\FarmPenStoreRequest;
use App\Http\Requests\Customer\Farms\FarmPenUpdateRequest;
use App\Models\Farm;
use App\Models\FarmPen;
use App\Models\LivestockPenFinancialEntry;
use App\Services\Livestock\LivestockPenProfitService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FarmPenController extends Controller
{
    public function index(string $locale): View
    {
        $tenantId = (string) auth()->user()->tenant_id;
        $status = request('status', 'active');

        $query = FarmPen::query()->where('tenant_id', $tenantId)->with('farm');

        if ($status === 'trashed') {
            $query->onlyTrashed();
        } elseif ($status === 'all') {
            $query->withTrashed();
        }

        if (request()->filled('farm_id')) {
            $query->where('farm_id', request('farm_id'));
        }

        if (request()->filled('type')) {
            $query->where('type', request('type'));
        }

        $pens = $query->latest()->paginate(15)->withQueryString();
        $farms = Farm::query()->where('tenant_id', $tenantId)->orderBy('name')->get();

        return view('dashboard.customer.farms.pens.index', compact('pens', 'farms', 'status'));
    }

    public function restore(string $locale, int|string $id): RedirectResponse
    {
        $tenantId = (string) auth()->user()->tenant_id;
        $pen = FarmPen::onlyTrashed()->where('tenant_id', $tenantId)->findOrFail($id);
        $pen->restore();

        return redirect()->route('customer.farm-pens.index', ['locale' => $locale, 'status' => 'trashed'])
            ->with('success', __('farms.messages.success.pen_restored') ?? 'تم استعادة الحظيرة بنجاح');
    }

    public function create(string $locale): View
    {
        $tenantId = (string) auth()->user()->tenant_id;
        $farms = Farm::query()->where('tenant_id', $tenantId)->orderBy('name')->get();

        return view('dashboard.customer.farms.pens.create', compact('farms'));
    }

    public function store(FarmPenStoreRequest $request, string $locale): RedirectResponse
    {
        $tenantId = (string) auth()->user()->tenant_id;
        
        $pen = FarmPen::query()->create([
            'tenant_id' => $tenantId,
            ...$request->validated(),
        ]);

        return redirect()->route('customer.farm-pens.show', ['locale' => $locale, 'farm_pen' => $pen->id])
            ->with('success', __('farms.messages.success.pen_created'));
    }

    public function show(string $locale, FarmPen $farm_pen, LivestockPenProfitService $profitService): View
    {
        $this->authorizeTenant($farm_pen);

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
        Request $request,
        string $locale,
        FarmPen $farm_pen
    ): RedirectResponse {
        $this->authorizeTenant($farm_pen);

        $validated = $request->validate([
            'type'       => ['required', 'string'],
            'amount'     => ['required', 'numeric', 'min:0.01'],
            'entry_date' => ['required', 'date'],
            'notes'      => ['nullable', 'string', 'max:500'],
        ]);

        LivestockPenFinancialEntry::query()->create([
            'tenant_id'  => (string) $farm_pen->tenant_id,
            'pen_id'     => $farm_pen->id,
            'type'       => $validated['type'],
            'amount'     => $validated['amount'],
            'entry_date' => $validated['entry_date'],
            'notes'      => $validated['notes'] ?? null,
        ]);

     return redirect()->route('customer.farm-pens.show', [
    'locale'   => $locale,
    'farm_pen' => $farm_pen->id,
])->with('success', __('farms.messages.success.financial_entry_recorded') ?? 'تم تسجيل القيد بنجاح');}
    public function edit(string $locale, FarmPen $farm_pen): View
    {
        $this->authorizeTenant($farm_pen);

        $tenantId = (string) auth()->user()->tenant_id;
        $farms = Farm::query()->where('tenant_id', $tenantId)->orderBy('name')->get();

        return view('dashboard.customer.farms.pens.edit', [
            'pen'   => $farm_pen,
            'farms' => $farms,
        ]);
    }

    public function update(FarmPenUpdateRequest $request, string $locale, FarmPen $farm_pen): RedirectResponse
    {
        $this->authorizeTenant($farm_pen);

        $farm_pen->update($request->validated());

        return redirect()->route('customer.farm-pens.show', ['locale' => $locale, 'farm_pen' => $farm_pen->id])
            ->with('success', __('farms.messages.success.pen_updated'));
    }

    public function destroy(string $locale, FarmPen $farm_pen): RedirectResponse
    {
        $this->authorizeTenant($farm_pen);

        $farm_pen->delete();

        return redirect()->route('customer.farm-pens.index', ['locale' => $locale])
            ->with('success', __('farms.messages.success.pen_deleted'));
    }

    private function authorizeTenant(FarmPen $farm_pen): void
    {
        $tenantId = (string) auth()->user()->tenant_id;
        if ((string) $farm_pen->tenant_id !== $tenantId) {
            abort(403);
        }
    }
}