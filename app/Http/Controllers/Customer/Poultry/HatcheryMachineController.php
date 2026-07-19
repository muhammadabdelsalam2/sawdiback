<?php

namespace App\Http\Controllers\Customer\Poultry;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\Poultry\HatcheryMachineStoreRequest;
use App\Http\Requests\Customer\Poultry\HatcheryMachineUpdateRequest;
use App\Models\Poultry\PoultryHatcheryMachine;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class HatcheryMachineController extends Controller
{
    public function index(): View
    {
        $machines = PoultryHatcheryMachine::query()->withCount('batches')->orderBy('machine_number')->paginate(15);

        return view('dashboard.customer.poultry.hatchery_machines.index', compact('machines'));
    }

    public function create(): View
    {
        return view('dashboard.customer.poultry.hatchery_machines.create');
    }

    public function store(HatcheryMachineStoreRequest $request, string $locale): RedirectResponse
    {
        PoultryHatcheryMachine::query()->create($request->validated());

        return redirect()->route('customer.poultry.hatchery-machines.index', ['locale' => $locale])
            ->with('success', __('poultry.messages.success.hatchery_machine_created'));
    }

    public function edit(string $locale, PoultryHatcheryMachine $hatchery_machine): View
    {
        return view('dashboard.customer.poultry.hatchery_machines.edit', ['machine' => $hatchery_machine]);
    }

    public function update(HatcheryMachineUpdateRequest $request, string $locale, PoultryHatcheryMachine $hatchery_machine): RedirectResponse
    {
        $hatchery_machine->update($request->validated());

        return redirect()->route('customer.poultry.hatchery-machines.index', ['locale' => $locale])
            ->with('success', __('poultry.messages.success.hatchery_machine_updated'));
    }

    public function destroy(string $locale, PoultryHatcheryMachine $hatchery_machine): RedirectResponse
    {
        $hatchery_machine->delete();

        return redirect()->route('customer.poultry.hatchery-machines.index', ['locale' => $locale])
            ->with('success', __('poultry.messages.success.hatchery_machine_deleted'));
    }
}
