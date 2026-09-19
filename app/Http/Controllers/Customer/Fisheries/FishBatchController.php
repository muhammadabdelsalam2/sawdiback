<?php

namespace App\Http\Controllers\Customer\Fisheries;

use App\Http\Controllers\Controller;
use App\Models\Farm;
use App\Models\FishBatch;
use App\Models\InventoryProduct;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class FishBatchController extends Controller
{
    /**
     * عرض قائمة أحواض ودفعات الأسماك مع الإحصائيات العامة
     */
    public function index(Request $request, string $locale): View
    {
        $tenantId = (string) auth()->user()->tenant_id;

        $query = FishBatch::query()
            ->where('tenant_id', $tenantId)
            ->with('farm:id,name');

        if ($request->filled('farm_id')) {
            $query->where('farm_id', $request->integer('farm_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('search')) {
            $term = $request->string('search');
            $query->where(function ($sub) use ($term) {
                $sub->where('pond_name', 'like', "%{$term}%")
                    ->orWhere('batch_code', 'like', "%{$term}%")
                    ->orWhere('fish_type', 'like', "%{$term}%");
            });
        }

        $batches = $query->latest('started_at')->paginate(15);

        // إحصائيات سريعة للبطاقات العلوية
        $stats = [
            'total_ponds'       => FishBatch::where('tenant_id', $tenantId)->count(),
            'active_ponds'      => FishBatch::where('tenant_id', $tenantId)->where('status', 'active')->count(),
            'total_live_fish'   => FishBatch::where('tenant_id', $tenantId)->where('status', 'active')->sum('current_count'),
            'total_feed_kg'     => FishBatch::where('tenant_id', $tenantId)->sum('feed_consumed_kg'),
            'total_cost'        => FishBatch::where('tenant_id', $tenantId)->sum('total_cost'),
        ];

        $farms = Farm::query()->where('tenant_id', $tenantId)->orderBy('name')->get(['id', 'name']);
        $currentLocale = $locale;

        return view('dashboard.customer.fisheries.index', compact('batches', 'stats', 'farms', 'currentLocale'));
    }

    /**
     * شاشة إضافة دورة / حوض سمكي جديد
     */
    public function create(string $locale): View
    {
        $tenantId = (string) auth()->user()->tenant_id;
        $farms = Farm::query()->where('tenant_id', $tenantId)->orderBy('name')->get(['id', 'name']);
        $currentLocale = $locale;

        return view('dashboard.customer.fisheries.create', compact('farms', 'currentLocale'));
    }

    /**
     * حفظ الحوض السمكي الجديد
     */
    public function store(Request $request, string $locale): RedirectResponse
    {
        $tenantId = (string) auth()->user()->tenant_id;

        $validated = $request->validate([
            'farm_id'          => ['required', 'integer', 'exists:farms,id'],
            'batch_code'       => ['nullable', 'string', 'max:50'],
            'pond_name'        => ['required', 'string', 'max:100'],
            'fish_type'        => ['required', 'string', 'max:100'],
            'initial_count'    => ['required', 'integer', 'min:1'],
            'initial_weight_g' => ['nullable', 'numeric', 'min:0'],
            'target_weight_g'  => ['nullable', 'numeric', 'min:0'],
            'started_at'       => ['required', 'date'],
            'total_cost'       => ['nullable', 'numeric', 'min:0'],
            'notes'            => ['nullable', 'string'],
        ]);

        $validated['tenant_id']        = $tenantId;
        $validated['current_count']    = $validated['initial_count'];
        $validated['mortality_count']  = 0;
        $validated['current_weight_g'] = $validated['initial_weight_g'] ?? 0;
        $validated['feed_consumed_kg'] = 0;
        $validated['status']           = 'active';

        if (empty($validated['batch_code'])) {
            $validated['batch_code'] = 'FISH-' . date('Ymd') . '-' . rand(100, 999);
        }

        $batch = FishBatch::create($validated);

        return redirect()
            ->route('customer.fisheries.show', ['locale' => $locale, 'fish_batch' => $batch->id])
            ->with('success', 'تم إنشاء حوض الأسماك وبدء الدورة بنجاح.');
    }

    /**
     * شاشة تفاصيل الحوض ومتابعة التغذية والنمو والنفوق
     */
    public function show(string $locale, FishBatch $fish_batch): View
    {
        $this->authorizeTenant($fish_batch);

        $tenantId = (string) auth()->user()->tenant_id;
        $fish_batch->load('farm:id,name');
        $currentLocale = $locale;

        // جلب قائمة الأعلاف المسجلة في المخزن لاختيار الصنف عند التغذية
        $feedProducts = InventoryProduct::query()
            ->where('tenant_id', $tenantId)
            ->orderBy('name')
            ->get(['id', 'name', 'current_stock', 'unit', 'purchase_price']);

        // حساب معدل النفوق ومؤشرات النمو
        $initial = max(1, (int)$fish_batch->initial_count);
        $mortalityRate = round(($fish_batch->mortality_count / $initial) * 100, 2);

        // تحويل استهلاك العلف إلى أطنان للعرض إن زاد عن 1000 كجم
        $feedTon = round($fish_batch->feed_consumed_kg / 1000, 3);

        return view('dashboard.customer.fisheries.show', [
            'batch'          => $fish_batch,
            'feedProducts'   => $feedProducts,
            'mortalityRate'  => $mortalityRate,
            'feedTon'        => $feedTon,
            'currentLocale'  => $currentLocale,
        ]);
    }

    /**
     * تعديل بيانات الحوض
     */
    public function edit(string $locale, FishBatch $fish_batch): View
    {
        $this->authorizeTenant($fish_batch);

        $tenantId = (string) auth()->user()->tenant_id;
        $farms = Farm::query()->where('tenant_id', $tenantId)->orderBy('name')->get(['id', 'name']);
        $batch = $fish_batch;
        $currentLocale = $locale;

        return view('dashboard.customer.fisheries.edit', compact('batch', 'farms', 'currentLocale'));
    }

    /**
     * تحديث بيانات الحوض
     */
    public function update(Request $request, string $locale, FishBatch $fish_batch): RedirectResponse
    {
        $this->authorizeTenant($fish_batch);

        $validated = $request->validate([
            'farm_id'          => ['required', 'integer', 'exists:farms,id'],
            'pond_name'        => ['required', 'string', 'max:100'],
            'fish_type'        => ['required', 'string', 'max:100'],
            'current_count'    => ['required', 'integer', 'min:0'],
            'mortality_count'  => ['nullable', 'integer', 'min:0'],
            'current_weight_g' => ['nullable', 'numeric', 'min:0'],
            'target_weight_g'  => ['nullable', 'numeric', 'min:0'],
            'feed_consumed_kg' => ['nullable', 'numeric', 'min:0'],
            'total_cost'       => ['nullable', 'numeric', 'min:0'],
            'status'           => ['required', 'in:active,harvested,paused'],
            'harvested_at'     => ['nullable', 'date'],
            'notes'            => ['nullable', 'string'],
        ]);

        $fish_batch->update($validated);

        return redirect()
            ->route('customer.fisheries.show', ['locale' => $locale, 'fish_batch' => $fish_batch->id])
            ->with('success', 'تم تحديث بيانات الحوض بنجاح.');
    }

    /**
     * حذف الحوض (Soft Delete)
     */
    public function destroy(string $locale, FishBatch $fish_batch): RedirectResponse
    {
        $this->authorizeTenant($fish_batch);
        $fish_batch->delete();

        return redirect()
            ->route('customer.fisheries.index', ['locale' => $locale])
            ->with('success', 'تم نقل الحوض السمكي إلى سلة المحذوفات بنجاح.');
    }

    /**
     * تسجيل استهلاك العلف مع الخصم التلقائي من المخزن وتحديث التكاليف
     */
    public function recordFeeding(Request $request, string $locale, FishBatch $fish_batch): RedirectResponse
    {
        $this->authorizeTenant($fish_batch);
        $tenantId = (string) auth()->user()->tenant_id;

        $validated = $request->validate([
            'product_id' => ['nullable', 'integer'],
            'quantity'   => ['required', 'numeric', 'min:0.01'],
            'unit'       => ['required', 'in:kg,ton'],
            'cost'       => ['nullable', 'numeric', 'min:0'],
            'feed_date'  => ['required', 'date'],
            'notes'      => ['nullable', 'string'],
        ]);

        // التحويل إلى كجم
        $quantityInKg = ($validated['unit'] === 'ton')
            ? ((float)$validated['quantity'] * 1000)
            : (float)$validated['quantity'];

        $cost = (float)($validated['cost'] ?? 0);

        DB::transaction(function () use ($fish_batch, $tenantId, $validated, $quantityInKg, &$cost) {
            // 1. الخصم من المخزون إن تم تحديد صنف العلف
            if (!empty($validated['product_id'])) {
                $product = InventoryProduct::where('tenant_id', $tenantId)->find($validated['product_id']);
                if ($product) {
                    $product->current_stock = max(0, $product->current_stock - $quantityInKg);
                    $product->save();

                    // احتساب التكلفة تلقائياً إن لم تُدخل يدوياً
                    if ($cost <= 0 && $product->purchase_price > 0) {
                        $cost = $quantityInKg * (float)$product->purchase_price;
                    }
                }
            }

            // 2. تحديث بيانات الحوض
            $fish_batch->feed_consumed_kg += $quantityInKg;
            $fish_batch->total_cost += $cost;
            $fish_batch->save();
        });

        return redirect()
            ->route('customer.fisheries.show', ['locale' => $locale, 'fish_batch' => $fish_batch->id])
            ->with('success', 'تم تسجيل استهلاك العلف وخصم الكمية من المخزون بنجاح.');
    }

    /**
     * تسجيل نفوق الأسماك وتحديث العدد الحي
     */
    public function recordMortality(Request $request, string $locale, FishBatch $fish_batch): RedirectResponse
    {
        $this->authorizeTenant($fish_batch);

        $validated = $request->validate([
            'count'          => ['required', 'integer', 'min:1'],
            'mortality_date' => ['required', 'date'],
            'notes'          => ['nullable', 'string'],
        ]);

        $deadCount = (int)$validated['count'];

        DB::transaction(function () use ($fish_batch, $deadCount) {
            $fish_batch->mortality_count += $deadCount;
            $fish_batch->current_count = max(0, $fish_batch->current_count - $deadCount);
            $fish_batch->save();
        });

        return redirect()
            ->route('customer.fisheries.show', ['locale' => $locale, 'fish_batch' => $fish_batch->id])
            ->with('success', 'تم تسجيل عدد النافق وتحديث الأعداد الحية للحوض.');
    }

    /**
     * حصاد وتسويق الأسماك وإغلاق الدورة
     */
    public function recordHarvest(Request $request, string $locale, FishBatch $fish_batch): RedirectResponse
    {
        $this->authorizeTenant($fish_batch);

        $validated = $request->validate([
            'harvest_date' => ['required', 'date'],
            'harvest_type' => ['required', 'in:total,partial'],
            'weight'       => ['required', 'numeric', 'min:0.01'],
            'unit'         => ['required', 'in:kg,ton'],
            'total_sales'  => ['required', 'numeric', 'min:0'],
            'notes'        => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($fish_batch, $validated) {
            $fish_batch->harvested_at = $validated['harvest_date'];
            if ($validated['harvest_type'] === 'total') {
                $fish_batch->status = 'harvested';
                $fish_batch->current_count = 0;
            }
            $fish_batch->save();
        });

        return redirect()
            ->route('customer.fisheries.show', ['locale' => $locale, 'fish_batch' => $fish_batch->id])
            ->with('success', 'تم تسجيل حصاد الأسماك وتحديث حالة الدورة بنجاح.');
    }

    /**
     * التحقق من تبعية الحوض للمستأجر الحالي
     */
    private function authorizeTenant(FishBatch $batch): void
    {
        $tenantId = (string) auth()->user()->tenant_id;
        if ((string) $batch->tenant_id !== $tenantId) {
            abort(403);
        }
    }
}
