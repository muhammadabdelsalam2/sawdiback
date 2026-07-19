<?php

namespace App\Http\Controllers\Customer\Poultry;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\Poultry\HatcheryBatchStoreRequest;
use App\Http\Requests\Customer\Poultry\HatcheryBatchUpdateRequest;
use App\Models\Poultry\PoultryHatcheryBatch;
use App\Models\Poultry\PoultryHatcheryMachine;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class HatcheryBatchController extends Controller
{
    public function index(): View
    {
        $batches = PoultryHatcheryBatch::query()->with('machine')->orderByDesc('loaded_at')->paginate(15);

        return view('dashboard.customer.poultry.hatchery_batches.index', compact('batches'));
    }

    public function create(): View
    {
        $machines = PoultryHatcheryMachine::query()->orderBy('machine_number')->get();

        return view('dashboard.customer.poultry.hatchery_batches.create', compact('machines'));
    }

    public function store(HatcheryBatchStoreRequest $request, string $locale): RedirectResponse
    {
        $batch = PoultryHatcheryBatch::query()->create($request->validated());

        return redirect()->route('customer.poultry.hatchery-batches.show', ['locale' => $locale, 'hatchery_batch' => $batch->id])
            ->with('success', __('poultry.messages.success.hatchery_batch_created'));
    }

    public function show(string $locale, PoultryHatcheryBatch $hatchery_batch): View
    {
        $hatchery_batch->load('machine');

        return view('dashboard.customer.poultry.hatchery_batches.show', ['batch' => $hatchery_batch]);
    }

    public function edit(string $locale, PoultryHatcheryBatch $hatchery_batch): View
    {
        $machines = PoultryHatcheryMachine::query()->orderBy('machine_number')->get();

        return view('dashboard.customer.poultry.hatchery_batches.edit', ['batch' => $hatchery_batch, 'machines' => $machines]);
    }

    public function update(HatcheryBatchUpdateRequest $request, string $locale, PoultryHatcheryBatch $hatchery_batch): RedirectResponse
    {
        $hatchery_batch->update($request->validated());

        return redirect()->route('customer.poultry.hatchery-batches.show', ['locale' => $locale, 'hatchery_batch' => $hatchery_batch->id])
            ->with('success', __('poultry.messages.success.hatchery_batch_updated'));
    }

    public function destroy(string $locale, PoultryHatcheryBatch $hatchery_batch): RedirectResponse
    {
        $hatchery_batch->delete();

        return redirect()->route('customer.poultry.hatchery-batches.index', ['locale' => $locale])
            ->with('success', __('poultry.messages.success.hatchery_batch_deleted'));
    }
}
