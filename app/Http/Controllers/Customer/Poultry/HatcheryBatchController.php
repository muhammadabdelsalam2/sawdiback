<?php

namespace App\Http\Controllers\Customer\Poultry;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\Poultry\HatcheryBatchStoreRequest;
use App\Http\Requests\Customer\Poultry\HatcheryBatchUpdateRequest;
use App\Models\FarmPen;
use App\Models\Poultry\PoultryChickenBreed;
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
    public function index(Request $request, string $locale): View
    {
        $tenantId = (string) auth()->user()->tenant_id;

        $batches = PoultryHatcheryBatch::query()
            ->where('tenant_id', $tenantId)
            ->with(['machine', 'pen.farm'])
            ->orderByDesc('loaded_at')
            ->paginate(15);

        return view('dashboard.customer.poultry.hatchery_batches.index', [
            'batches'       => $batches,
            'currentLocale' => $locale,
        ]);
    }

    public function create(string $locale): View
    {
        $tenantId = (string) auth()->user()->tenant_id;

        $machines = PoultryHatcheryMachine::query()
            ->where('tenant_id', $tenantId)
            ->orderBy('machine_number')
            ->get();

        $pens = FarmPen::query()
            ->where('tenant_id', $tenantId)
            ->with('farm')
            ->forSelect()
            ->get();

        $breeds = PoultryChickenBreed::query()
            ->where('tenant_id', $tenantId)
            ->orderBy('code')
            ->get();

        return view('dashboard.customer.poultry.hatchery_batches.create', [
            'machines'      => $machines,
            'pens'          => $pens,
            'breeds'        => $breeds,
            'currentLocale' => $locale,
        ]);
    }

    public function store(HatcheryBatchStoreRequest $request, string $locale): RedirectResponse
    {
        $data = $request->validated();
        $data['tenant_id'] = (string) auth()->user()->tenant_id;

        $batch = PoultryHatcheryBatch::query()->create($data);

        $this->syncBatchBreeds($batch, $request);

        return redirect()
            ->route('customer.poultry.hatchery-batches.show', ['locale' => $locale, 'hatchery_batch' => $batch->id])
            ->with('success', __('poultry.messages.success.hatchery_batch_created') ?? 'تم إنشاء دفعة التفقيس بنجاح.');
    }

    public function show(string $locale, PoultryHatcheryBatch $hatchery_batch, PoultryFinancialService $financialService): View
    {
        $tenantId = (string) auth()->user()->tenant_id;
        if ((string) $hatchery_batch->tenant_id !== $tenantId) {
            abort(403);
        }

        $hatchery_batch->load([
            'machine',
            'pen.farm',
            'breeds',
            'dailyLogs' => fn ($q) => $q->orderByDesc('log_date'),
        ]);

        $financials = $financialService->calculateBatchProfitLoss($hatchery_batch);

        return view('dashboard.customer.poultry.hatchery_batches.show', [
            'batch'         => $hatchery_batch,
            'financials'    => $financials,
            'currentLocale' => $locale,
        ]);
    }

    public function edit(string $locale, PoultryHatcheryBatch $hatchery_batch): View
    {
        $tenantId = (string) auth()->user()->tenant_id;
        if ((string) $hatchery_batch->tenant_id !== $tenantId) {
            abort(403);
        }

        $machines = PoultryHatcheryMachine::query()
            ->where('tenant_id', $tenantId)
            ->orderBy('machine_number')
            ->get();

        $pens = FarmPen::query()
            ->where('tenant_id', $tenantId)
            ->with('farm')
            ->forSelect()
            ->get();

        $breeds = PoultryChickenBreed::query()
            ->where('tenant_id', $tenantId)
            ->orderBy('code')
            ->get();

        return view('dashboard.customer.poultry.hatchery_batches.edit', [
            'hatchery_batch' => $hatchery_batch,
            'machines'       => $machines,
            'pens'           => $pens,
            'breeds'         => $breeds,
            'currentLocale'  => $locale,
        ]);
    }

    public function update(HatcheryBatchUpdateRequest $request, string $locale, PoultryHatcheryBatch $hatchery_batch, PoultryFinancialService $financialService): RedirectResponse
    {
        $tenantId = (string) auth()->user()->tenant_id;
        if ((string) $hatchery_batch->tenant_id !== $tenantId) {
            abort(403);
        }

        $hatchery_batch->update($request->validated());

        $this->syncBatchBreeds($hatchery_batch, $request);

        $hatchedCount = (int) ($hatchery_batch->chicks_hatched ?? $hatchery_batch->chicks_produced ?? 0);
        if ($hatchery_batch->actual_hatch_at && $hatchedCount > 0) {
            $financialService->recordBatchJournalEntry($hatchery_batch, auth()->id() ?? 1);
        }

        return redirect()
            ->route('customer.poultry.hatchery-batches.show', ['locale' => $locale, 'hatchery_batch' => $hatchery_batch->id])
            ->with('success', __('poultry.messages.success.hatchery_batch_updated') ?? 'تم تحديث دفعة التفقيس بنجاح.');
    }

    public function destroy(string $locale, PoultryHatcheryBatch $hatchery_batch): RedirectResponse
    {
        $tenantId = (string) auth()->user()->tenant_id;
        if ((string) $hatchery_batch->tenant_id !== $tenantId) {
            abort(403);
        }

        $hatchery_batch->delete();

        return redirect()
            ->route('customer.poultry.hatchery-batches.index', ['locale' => $locale])
            ->with('success', __('poultry.messages.success.hatchery_batch_deleted') ?? 'تم حذف دفعة التفقيس بنجاح.');
    }

    public function storeDailyLog(Request $request, string $locale, PoultryHatcheryBatch $hatchery_batch): RedirectResponse
    {
        $tenantId = (string) auth()->user()->tenant_id;
        if ((string) $hatchery_batch->tenant_id !== $tenantId) {
            abort(403);
        }

        $validated = $request->validate([
            'log_date'                => ['required', 'date'],
            'temperature'             => ['nullable', 'numeric'],
            'humidity'                => ['nullable', 'numeric'],
            'has_incident'            => ['nullable', 'boolean'],
            'stoppage_duration_hours' => ['nullable', 'numeric', 'min:0'],
            'incident_reason'         => ['nullable', 'string', 'max:1000'],
            'notes'                   => ['nullable', 'string', 'max:1000'],
        ]);

        $hasIncident = $request->boolean('has_incident') || $request->boolean('has_stoppage');
        $duration = $request->filled('stoppage_duration_hours')
            ? (float) $request->input('stoppage_duration_hours')
            : ($request->filled('stoppage_minutes') ? ((float) $request->input('stoppage_minutes') / 60) : null);

        $reason = $request->input('incident_reason') ?? $request->input('stoppage_reason');

        PoultryHatcheryDailyLog::query()->create([
            'tenant_id'               => $tenantId,
            'hatchery_batch_id'       => $hatchery_batch->id,
            'log_date'                => $validated['log_date'],
            'temperature'             => $validated['temperature'] ?? null,
            'humidity'                => $validated['humidity'] ?? null,
            'has_incident'            => $hasIncident,
            'stoppage_duration_hours' => $duration,
            'incident_reason'         => $reason,
            'notes'                   => $validated['notes'] ?? null,
        ]);

        return redirect()->back()->with('success', __('poultry.messages.success.daily_log_recorded') ?? 'تم تسجيل قراءة المتابعة اليومية بنجاح.');
    }

    public function profitLoss(PoultryHatcheryBatch $hatchery_batch, PoultryFinancialService $financialService): JsonResponse
    {
        $summary = $financialService->calculateBatchProfitLoss($hatchery_batch);

        return response()->json([
            'success' => true,
            'data'    => $summary,
        ]);
    }

    private function syncBatchBreeds(PoultryHatcheryBatch $batch, Request $request): void
    {
        if (method_exists($batch, 'breeds')) {
            try {
                $syncData = [];

                if ($request->has('batch_breeds') && is_array($request->input('batch_breeds'))) {
                    foreach ($request->input('batch_breeds') as $key => $item) {
                        $count = (int) ($item['count'] ?? 0);
                        if ($count > 0) {
                            $syncData[$key] = ['egg_count' => $count];
                        }
                    }
                } elseif ($request->has('breeds') && is_array($request->input('breeds'))) {
                    foreach ($request->input('breeds') as $item) {
                        if (!empty($item['breed_id']) && isset($item['egg_count'])) {
                            $syncData[$item['breed_id']] = ['egg_count' => (int) $item['egg_count']];
                        }
                    }
                }

                if (!empty($syncData)) {
                    $batch->breeds()->sync($syncData);
                }
            } catch (\Throwable $e) {
                // تجاوز الخطأ في حال عدم وجود جدول وسيط
            }
        }
    }
}