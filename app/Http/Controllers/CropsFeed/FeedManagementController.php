<?php

namespace App\Http\Controllers\CropsFeed;

use App\Http\Controllers\Controller;
use App\Http\Requests\CropsFeed\CropFeedAllocationStoreRequest;
use App\Http\Requests\CropsFeed\CropSeedlingStockStoreRequest;
use App\Http\Requests\CropsFeed\FeedConsumptionStoreRequest;
use App\Http\Requests\CropsFeed\FeedReportRequest;
use App\Http\Requests\CropsFeed\FeedStockMovementStoreRequest;
use App\Models\Crop;
use App\Models\CropSeedlingStock;
use App\Models\Farm;
use App\Models\FarmPen;
use App\Models\FeedConsumption;
use App\Models\FeedType;
use App\Models\LivestockAnimal;
use App\Services\CropsFeed\AllocateCropToFeedService;
use App\Services\CropsFeed\FeedStockService;
use App\Services\CropsFeed\RecordFeedConsumptionService;
use App\Services\CropsFeed\RecordFeedStockMovementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use RuntimeException;

class FeedManagementController extends Controller
{
    public function __construct(
        private readonly FeedStockService $feedStockService,
        private readonly RecordFeedStockMovementService $recordFeedStockMovementService,
        private readonly RecordFeedConsumptionService $recordFeedConsumptionService,
        private readonly AllocateCropToFeedService $allocateCropToFeedService
    ) {
    }

    public function index(string $locale): View
    {
        $tenantId = (string) auth()->user()->tenant_id;

        $feedTypes = FeedType::query()->where('tenant_id', $tenantId)->orderBy('name')->get();
        $crops = Crop::query()->where('tenant_id', $tenantId)->orderByDesc('id')->get();
        $animals = LivestockAnimal::query()->where('tenant_id', $tenantId)->orderBy('tag_number')->get();
        $pens = FarmPen::query()->where('tenant_id', $tenantId)->with('farm')->get();
        $farms = Farm::query()->where('tenant_id', $tenantId)->orderBy('name')->get();

        $recentConsumptions = FeedConsumption::query()
            ->where('tenant_id', $tenantId)
            ->with(['feedType', 'animal', 'pen'])
            ->orderByDesc('id')
            ->limit(20)
            ->get();

        $seedlingStocks = CropSeedlingStock::query()
            ->where('tenant_id', $tenantId)
            ->with('farm')
            ->orderBy('name')
            ->get();

        $stocks = $feedTypes->map(function (FeedType $feedType) {
            $onHand = $this->feedStockService->stockOnHand($feedType->id);
            return [
                'feedType' => $feedType,
                'stock_on_hand' => $onHand,
                'is_low_stock' => $onHand <= (float) $feedType->low_stock_threshold,
            ];
        });

        // الأقسام المستهدفة لاستهلاك وتكاليف الأعلاف
        $targetSections = [
            'poultry_broiler' => __('crops_feed.sections.poultry_broiler') ?? 'دواجن - لاحم',
            'poultry_layer'   => __('crops_feed.sections.poultry_layer') ?? 'دواجن - بياض',
            'breeding'        => __('crops_feed.sections.breeding') ?? 'سلالات وأمهات',
            'goats'           => __('crops_feed.sections.goats') ?? 'الماعز',
            'rabbits'         => __('crops_feed.sections.rabbits') ?? 'الأرانب',
            'fish'            => __('crops_feed.sections.fish') ?? 'الأسماك',
            'other'           => __('crops_feed.sections.other') ?? 'أخرى',
        ];

        return view('dashboard.crops_feed.feed.index', compact(
            'feedTypes',
            'stocks',
            'recentConsumptions',
            'animals',
            'pens',
            'crops',
            'seedlingStocks',
            'farms',
            'targetSections'
        ));
    }

    public function storeStockMovement(FeedStockMovementStoreRequest $request, string $locale): RedirectResponse
    {
        try {
            $this->recordFeedStockMovementService->execute($request->validated());
            return redirect()->back()->with('success', __('crops_feed.messages.success.feed_stock_movement_recorded') ?? 'تم تسجيل حركة المخزون بنجاح');
        } catch (RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function storeConsumption(FeedConsumptionStoreRequest $request, string $locale): RedirectResponse
    {
        try {
            $this->recordFeedConsumptionService->execute($request->validated());
            return redirect()->back()->with('success', __('crops_feed.messages.success.feed_consumption_recorded') ?? 'تم تسجيل استهلاك وتكلفة العلف بنجاح');
        } catch (RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function storeCropAllocation(CropFeedAllocationStoreRequest $request, string $locale): RedirectResponse
    {
        try {
            $this->allocateCropToFeedService->execute($request->validated());
            return redirect()->back()->with('success', __('crops_feed.messages.success.crop_allocated_to_feed') ?? 'تم تحويل المحصول إلى علف بنجاح');
        } catch (RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function storeSeedlingStock(CropSeedlingStockStoreRequest $request, string $locale): RedirectResponse
    {
        $tenantId = (string) auth()->user()->tenant_id;
        $data = $request->validated();
        $data['tenant_id'] = $tenantId;
        $data['low_stock_threshold'] = $data['low_stock_threshold'] ?? 0;

        CropSeedlingStock::query()->create($data);

        return redirect()->back()->with('success', __('crops_feed.messages.success.seedling_stock_recorded') ?? 'تم تسجيل مخزون الشتلات بنجاح');
    }

    public function reports(FeedReportRequest $request, string $locale): View
    {
        $tenantId = (string) auth()->user()->tenant_id;
        $month = $request->validated('month') ?: now()->format('Y-m');
        $start = now()->setDate((int) substr($month, 0, 4), (int) substr($month, 5, 2), 1)->startOfMonth();
        $end = (clone $start)->endOfMonth();

        // إجمالي تكلفة العلف الشهرية
        $monthlyFeedCost = (float) FeedConsumption::query()
            ->where('tenant_id', $tenantId)
            ->whereBetween('consumption_date', [$start->toDateString(), $end->toDateString()])
            ->sum('total_cost');

        // توزيع التكلفة والكميات بحسب الأقسام الإنتاجية
        $costByDepartment = FeedConsumption::query()
            ->where('tenant_id', $tenantId)
            ->whereBetween('consumption_date', [$start->toDateString(), $end->toDateString()])
            ->selectRaw('target_section, SUM(quantity) as total_quantity, SUM(total_cost) as total_cost')
            ->groupBy('target_section')
            ->get();

        // تكلفة العلف بحسب الحيوان الفردي (إن وجد)
        $costPerAnimal = FeedConsumption::query()
            ->where('tenant_id', $tenantId)
            ->selectRaw('animal_id, SUM(quantity) as total_quantity, SUM(total_cost) as total_cost')
            ->whereNotNull('animal_id')
            ->whereBetween('consumption_date', [$start->toDateString(), $end->toDateString()])
            ->groupBy('animal_id')
            ->with('animal')
            ->get();

        // التنبيه بالمخزون المنخفض
        $feedTypes = FeedType::query()->where('tenant_id', $tenantId)->orderBy('name')->get();
        $lowStockRows = $feedTypes->filter(function (FeedType $feedType) {
            $stock = $this->feedStockService->stockOnHand($feedType->id);
            return $stock <= (float) $feedType->low_stock_threshold;
        })->map(function (FeedType $feedType) {
            return [
                'feedType' => $feedType,
                'stock_on_hand' => $this->feedStockService->stockOnHand($feedType->id),
            ];
        });

        // إنتاج المزرعة المحول لأعلاف واحتياج الاستهلاك
        $feedProduction = (float) Crop::query()->where('tenant_id', $tenantId)->sum('available_for_feed_tons');
        $feedNeed = (float) FeedConsumption::query()
            ->where('tenant_id', $tenantId)
            ->whereBetween('consumption_date', [$start->toDateString(), $end->toDateString()])
            ->sum('quantity');

        return view('dashboard.crops_feed.reports.index', compact(
            'month',
            'monthlyFeedCost',
            'costByDepartment',
            'costPerAnimal',
            'lowStockRows',
            'feedProduction',
            'feedNeed'
        ));
    }
}
