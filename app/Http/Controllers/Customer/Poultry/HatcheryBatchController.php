<?php

namespace App\Http\Controllers\Customer\Poultry;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\Poultry\HatcheryBatchStoreRequest;
use App\Http\Requests\Customer\Poultry\HatcheryBatchUpdateRequest;
use App\Models\FarmPen;
use App\Models\Poultry\ChickenBreed;
use App\Models\Poultry\PoultryHatcheryBatch;
use App\Models\Poultry\PoultryHatcheryDailyLog;
use App\Models\Poultry\PoultryHatcheryMachine;
use App\Services\Poultry\PoultryFinancialService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HatcheryBatchController extends Controller
{
    public function index(): View
    {
        $batches = PoultryHatcheryBatch::query()
            ->with(['machine', 'breeds'])
            ->orderByDesc('loaded_at')
            ->paginate(15);

        return view('dashboard.customer.poultry.hatchery_batches.index', compact('batches'));
    }

    public function create(): View
    {
        $machines = PoultryHatcheryMachine::query()->orderBy('machine_number')->get();
        $pens = FarmPen::query()->forSelect()->get();
        $breeds = ChickenBreed::query()->orderBy('name')->get();

        return view('dashboard.customer.poultry.hatchery_batches.create', compact('machines', 'pens', 'breeds'));
    }

    public function store(HatcheryBatchStoreRequest $request, string $locale): RedirectResponse
    {
        $data = $request->validated();
        $batch = PoultryHatcheryBatch::query()->create($data);

        // ربط السلالات وكميات البيض إن وُجدت
        if ($request->filled('breeds') && is_array($request->input('breeds'))) {
            $syncData = [];
            foreach ($request->input('breeds') as $item) {
                if (!empty($item['breed_id']) && isset($item['egg_count'])) {
                    $syncData[$item['breed_id']] = ['egg_count' => (int) $item['egg_count']];
                }
            }
            if (!empty($syncData)) {
                $batch->breeds()->sync($syncData);
            }
        }

        return redirect()
            ->route('customer.poultry.hatchery-batches.show', ['locale' => $locale, 'hatchery_batch' => $batch->id])
            ->with('success', __('poultry.messages.success.hatchery_batch_created'));
    }

    public function show(string $locale, PoultryHatcheryBatch $hatchery_batch, PoultryFinancialService $financialService): View
    {
        $hatchery_batch->load([
            'machine',
            'pen',
            'farmPen',
            'breeds',
            'dailyLogs' => fn ($q) => $q->orderByDesc('log_date'),
        ]);

        $financials = $financialService->calculateBatchProfitLoss($hatchery_batch);

        return view('dashboard.customer.poultry.hatchery_batches.show', [
            'batch'      => $hatchery_batch,
            'financials' => $financials,
        ]);
    }

    public function edit(string $locale, PoultryHatcheryBatch $hatchery_batch): View
    {
        $machines = PoultryHatcheryMachine::query()->orderBy('machine_number')->get();
        $pens = FarmPen::query()->forSelect()->get();
        $breeds = ChickenBreed::query()->orderBy('name')->get();
        $hatchery_batch->load('breeds');

        return view('dashboard.customer.poultry.hatchery_batches.edit', compact('hatchery_batch', 'machines', 'pens', 'breeds'));
    }

    public function update(HatcheryBatchUpdateRequest $request, string $locale, PoultryHatcheryBatch $hatchery_batch, PoultryFinancialService $financialService): RedirectResponse
    {
        $hatchery_batch->update($request->validated());

        // مزامنة السلالات وكمياتها
        if ($request->has('breeds') && is_array($request->input('breeds'))) {
            $syncData = [];
            foreach ($request->input('breeds') as $item) {
                if (!empty($item['breed_id']) && isset($item['egg_count'])) {
                    $syncData[$item['breed_id']] = ['egg_count' => (int) $item['egg_count']];
                }
            }
            $hatchery_batch->breeds()->sync($syncData);
        }

        // ترحيل القيد إذا اكتمل التفقيس
        if ($hatchery_batch->actual_hatch_at && $hatchery_batch->chicks_produced > 0) {
            $financialService->recordBatchJournalEntry($hatchery_batch, auth()->id() ?? 1);
        }

        return redirect()
            ->route('customer.poultry.hatchery-batches.show', ['locale' => $locale, 'hatchery_batch' => $hatchery_batch->id])
            ->with('success', __('poultry.messages.success.hatchery_batch_updated'));
    }

    public function destroy(string $locale, PoultryHatcheryBatch $hatchery_batch): RedirectResponse
    {
        $hatchery_batch->delete();

        return redirect()
            ->route('customer.poultry.hatchery-batches.index', ['locale' => $locale])
            ->with('success', __('poultry.messages.success.hatchery_batch_deleted'));
    }

    public function storeDailyLog(Request $request, string $locale, PoultryHatcheryBatch $hatchery_batch): RedirectResponse
    {
        $validated = $request->validate([
            'log_date'         => ['required', 'date'],
            'temperature'      => ['nullable', 'numeric'],
            'humidity'         => ['nullable', 'numeric'],
            'has_stoppage'     => ['nullable', 'boolean'],
            'stoppage_minutes' => ['nullable', 'integer', 'min:0'],
            'stoppage_reason'  => ['nullable', 'string', 'max:1000'],
            'notes'            => ['nullable', 'string', 'max:1000'],
        ]);

        $validated['has_stoppage'] = $request->boolean('has_stoppage');

        PoultryHatcheryDailyLog::query()->create([
            'hatchery_batch_id' => $hatchery_batch->id,
            ...$validated,
        ]);

        return redirect()->back()->with('success', __('poultry.messages.success.daily_log_recorded'));
    }

    public function profitLoss(PoultryHatcheryBatch $hatchery_batch, PoultryFinancialService $financialService): JsonResponse
    {
        $summary = $financialService->calculateBatchProfitLoss($hatchery_batch);

        return response()->json([
            'success' => true,
            'data'    => $summary,
        ]);
    }
}