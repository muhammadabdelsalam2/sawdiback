<?php

namespace App\Http\Controllers\Customer\Poultry;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\Poultry\PoultryVehicleStoreRequest;
use App\Http\Requests\Customer\Poultry\PoultryVehicleUpdateRequest;
use App\Models\Farm;
use App\Models\Poultry\PoultryTransportVehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PoultryVehicleController extends Controller
{
    public function index(string $locale): View
    {
        $tenantId = (string) auth()->user()->tenant_id;

        $vehicles = PoultryTransportVehicle::query()
            ->where('tenant_id', $tenantId)
            ->with('farm')
            ->orderByDesc('id')
            ->paginate(15);

        $currentLocale = $locale;

        return view('dashboard.customer.poultry.vehicles.index', compact('vehicles', 'currentLocale'));
    }

    public function create(string $locale): View
    {
        $tenantId = (string) auth()->user()->tenant_id;

        $farms = Farm::query()
            ->where('tenant_id', $tenantId)
            ->orderBy('name')
            ->get();

        $currentLocale = $locale;

        return view('dashboard.customer.poultry.vehicles.create', compact('farms', 'currentLocale'));
    }

   public function store(PoultryVehicleStoreRequest $request, string $locale): RedirectResponse
    {
        $data = $request->validated();
        $data['tenant_id'] = auth()->user()->tenant_id;

        $vehicle = PoultryTransportVehicle::query()->create($data);

        return redirect()
            ->route('customer.poultry.vehicles.show', ['locale' => $locale, 'vehicle' => $vehicle->id])
            ->with('success', __('poultry.messages.success.vehicle_created') ?? __('poultry.messages.success.saved'));
    }

  public function show(string $locale, PoultryTransportVehicle $vehicle): View
    {
        $vehicle->load(['farm', 'rentals' => fn($q) => $q->latest('started_at')]);
        $currentLocale = $locale;

        return view('dashboard.customer.poultry.vehicles.show', compact('vehicle', 'currentLocale'));
    }
 public function edit(string $locale, PoultryTransportVehicle $vehicle): View
    {
        $farms = Farm::query()
            ->orderBy('name')
            ->get();

        $currentLocale = $locale;

        return view('dashboard.customer.poultry.vehicles.edit', compact('vehicle', 'farms', 'currentLocale'));
    }

    public function update(PoultryVehicleUpdateRequest $request, string $locale, PoultryTransportVehicle $vehicle): RedirectResponse
    {
        $vehicle->update($request->validated());

        return redirect()
            ->route('customer.poultry.vehicles.show', ['locale' => $locale, 'vehicle' => $vehicle->id])
            ->with('success', __('poultry.messages.success.vehicle_updated') ?? __('poultry.messages.success.saved'));
    }
    public function destroy(string $locale, PoultryTransportVehicle $vehicle): RedirectResponse
    {
        $tenantId = (string) auth()->user()->tenant_id;
        if ((string) $vehicle->tenant_id !== $tenantId) {
            abort(403);
        }

        $vehicle->delete();

        return redirect()
            ->route('customer.poultry.vehicles.index', ['locale' => $locale])
            ->with('success', __('poultry.messages.success.deleted') ?? 'تم حذف السيارة بنجاح');
    }
}