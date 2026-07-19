<?php

namespace App\Http\Controllers\Customer\Farms;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\Farms\FarmStoreRequest;
use App\Http\Requests\Customer\Farms\FarmUpdateRequest;
use App\Models\Farm;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FarmController extends Controller
{
    public function index(): View
    {
        $farms = Farm::query()->withCount('pens')->orderBy('name')->paginate(15);

        return view('dashboard.customer.farms.farms.index', compact('farms'));
    }

    public function create(): View
    {
        return view('dashboard.customer.farms.farms.create');
    }

    public function store(FarmStoreRequest $request, string $locale): RedirectResponse
    {
        Farm::query()->create($request->validated());

        return redirect()->route('customer.farms.index', ['locale' => $locale])
            ->with('success', __('farms.messages.success.farm_created'));
    }

    public function edit(string $locale, Farm $farm): View
    {
        return view('dashboard.customer.farms.farms.edit', compact('farm'));
    }

    public function update(FarmUpdateRequest $request, string $locale, Farm $farm): RedirectResponse
    {
        $farm->update($request->validated());

        return redirect()->route('customer.farms.index', ['locale' => $locale])
            ->with('success', __('farms.messages.success.farm_updated'));
    }

    public function destroy(string $locale, Farm $farm): RedirectResponse
    {
        $farm->delete();

        return redirect()->route('customer.farms.index', ['locale' => $locale])
            ->with('success', __('farms.messages.success.farm_deleted'));
    }
}
