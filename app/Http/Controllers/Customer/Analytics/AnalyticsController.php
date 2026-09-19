<?php

namespace App\Http\Controllers\Customer\Analytics;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\FishBatch;
use App\Models\InventoryProduct;
use App\Services\Analytics\AnalyticsService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
    public function __construct(private readonly AnalyticsService $analyticsService)
    {
    }

    public function index(string $locale): View
    {
        $tenantId = (string) auth()->user()?->tenant_id;
        $report = $this->analyticsService->dashboard($tenantId);

        $today = Carbon::today();
        $threshold60Days = $today->copy()->addDays(60);

        // 1. فحص الأعمدة المتاحة لتواريخ انتهاء الوثائق بشكل ديناميكي وآمن
        $employeeTable = (new Employee())->getTable();
        $possibleDateColumns = [
            'id_expiry_date',
            'national_id_expiry',
            'iqama_expiry_date',
            'contract_end_date',
            'passport_expiry_date'
        ];

        $validColumns = array_filter($possibleDateColumns, function ($col) use ($employeeTable) {
            return Schema::hasColumn($employeeTable, $col);
        });

        $expiringDocsCount = 0;
        if (!empty($validColumns)) {
            $expiringDocsCount = Employee::query()
                ->where('tenant_id', $tenantId)
                ->where('status', 'active')
                ->where(function ($query) use ($validColumns, $threshold60Days) {
                    foreach ($validColumns as $col) {
                        $query->orWhere(function ($sub) use ($col, $threshold60Days) {
                            $sub->whereNotNull($col)
                                ->where($col, '<=', $threshold60Days);
                        });
                    }
                })
                ->count();
        }

        // 2. عداد الأصناف التي بلغت أو انخفضت عن حد إعادة الطلب
        $lowStockCount = 0;
        if (Schema::hasTable('inventory_products') && Schema::hasColumn('inventory_products', 'current_stock') && Schema::hasColumn('inventory_products', 'min_stock')) {
            $lowStockCount = InventoryProduct::query()
                ->where('tenant_id', $tenantId)
                ->whereColumn('current_stock', '<=', 'min_stock')
                ->count();
        }

        // 3. ملخص دورات الاستزراع السمكي
        $fishSummary = [
            'active_ponds' => 0,
            'live_fish'    => 0,
            'total_feed'   => 0,
        ];

        if (Schema::hasTable('fish_batches')) {
            $fishSummary = [
                'active_ponds' => FishBatch::where('tenant_id', $tenantId)->where('status', 'active')->count(),
                'live_fish'    => FishBatch::where('tenant_id', $tenantId)->where('status', 'active')->sum('current_count'),
                'total_feed'   => FishBatch::where('tenant_id', $tenantId)->sum('feed_consumed_kg'),
            ];
        }

        $currentLocale = $locale;

        return view('dashboard.customer.analytics.index', compact(
            'report',
            'expiringDocsCount',
            'lowStockCount',
            'fishSummary',
            'currentLocale'
        ));
    }
}
