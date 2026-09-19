<?php

namespace App\Http\Controllers\Customer\Poultry;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\Poultry\HatcheryMachineStoreRequest;
use App\Http\Requests\Customer\Poultry\HatcheryMachineUpdateRequest;
use App\Models\Farm;
use App\Models\Poultry\PoultryHatcheryMachine;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class HatcheryMachineController extends Controller
{
    public function index(string $locale): View
    {
        $tenantId = (string) auth()->user()->tenant_id;

        $machines = PoultryHatcheryMachine::query()
            ->when(Schema::hasColumn('poultry_hatchery_machines', 'tenant_id'), fn ($q) => $q->where('tenant_id', $tenantId))
            ->with('farm')
            ->withCount('batches')
            ->orderBy('machine_number')
            ->paginate(15);

        return view('dashboard.customer.poultry.hatchery_machines.index', [
            'machines'      => $machines,
            'currentLocale' => $locale,
        ]);
    }

    public function create(string $locale): View
    {
        $tenantId = (string) auth()->user()->tenant_id;

        $farms = Farm::query()
            ->when(Schema::hasColumn('farms', 'tenant_id'), fn ($q) => $q->where('tenant_id', $tenantId))
            ->orderBy('name')
            ->get();

        return view('dashboard.customer.poultry.hatchery_machines.create', [
            'farms'         => $farms,
            'currentLocale' => $locale,
        ]);
    }

    public function store(HatcheryMachineStoreRequest $request, string $locale): RedirectResponse
    {
        $data = $request->validated();
        if (Schema::hasColumn('poultry_hatchery_machines', 'tenant_id')) {
            $data['tenant_id'] = (string) auth()->user()->tenant_id;
        }

        PoultryHatcheryMachine::query()->create($data);

        return redirect()->route('customer.poultry.hatchery-machines.index', ['locale' => $locale])
            ->with('success', __('poultry.messages.success.hatchery_machine_created') ?? 'تم تسجيل ماكينة التفقيس بنجاح.');
    }

    public function edit(string $locale, PoultryHatcheryMachine $hatchery_machine): View
    {
        $tenantId = (string) auth()->user()->tenant_id;
        if (Schema::hasColumn('poultry_hatchery_machines', 'tenant_id') && (string) $hatchery_machine->tenant_id !== $tenantId) {
            abort(403);
        }

        $farms = Farm::query()
            ->when(Schema::hasColumn('farms', 'tenant_id'), fn ($q) => $q->where('tenant_id', $tenantId))
            ->orderBy('name')
            ->get();

        return view('dashboard.customer.poultry.hatchery_machines.edit', [
            'machine'       => $hatchery_machine,
            'farms'         => $farms,
            'currentLocale' => $locale,
        ]);
    }

    public function update(HatcheryMachineUpdateRequest $request, string $locale, PoultryHatcheryMachine $hatchery_machine): RedirectResponse
    {
        $tenantId = (string) auth()->user()->tenant_id;
        if (Schema::hasColumn('poultry_hatchery_machines', 'tenant_id') && (string) $hatchery_machine->tenant_id !== $tenantId) {
            abort(403);
        }

        $hatchery_machine->update($request->validated());

        return redirect()->route('customer.poultry.hatchery-machines.index', ['locale' => $locale])
            ->with('success', __('poultry.messages.success.hatchery_machine_updated') ?? 'تم تحديث بيانات الماكينة بنجاح.');
    }

    public function destroy(string $locale, PoultryHatcheryMachine $hatchery_machine): RedirectResponse
    {
        $tenantId = (string) auth()->user()->tenant_id;
        if (Schema::hasColumn('poultry_hatchery_machines', 'tenant_id') && (string) $hatchery_machine->tenant_id !== $tenantId) {
            abort(403);
        }

        $hatchery_machine->delete();

        return redirect()->route('customer.poultry.hatchery-machines.index', ['locale' => $locale])
            ->with('success', __('poultry.messages.success.hatchery_machine_deleted') ?? 'تم حذف الماكينة بنجاح.');
    }
}