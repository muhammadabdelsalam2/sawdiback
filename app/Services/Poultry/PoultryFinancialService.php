<?php

namespace App\Services\Poultry;

use App\Models\Poultry\PoultryHatcheryBatch;
use App\Models\Poultry\PoultryVehicleRental;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PoultryFinancialService
{
    /**
     * استرجاع كلاس موديل القيود اليومية الفعلي بالمشروع بأمان
     */
    protected function getJournalEntryClass(): ?string
    {
        $classes = [
            'App\Models\Customer\Finance\JournalEntry',
            'App\Models\Finance\JournalEntry',
            'App\Models\JournalEntry',
        ];

        foreach ($classes as $class) {
            if (class_exists($class)) {
                return $class;
            }
        }

        return null;
    }

    /**
     * حساب تكاليف وأرباح ومؤشرات دورة التسمين
     */
    public function calculateBroilerCycleMetrics($cycle): array
    {
        $initialChicks = (int) ($cycle->initial_chicks_count ?? $cycle->chicks_count ?? 0);

        // عمود النفوق
        $mortalityCol = Schema::hasColumn('poultry_broiler_mortalities', 'quantity') ? 'quantity' : 
                       (Schema::hasColumn('poultry_broiler_mortalities', 'count') ? 'count' : 'mortality_count');
        $totalMortality = (int) ($cycle->mortalities()->sum($mortalityCol) ?? 0);

        $currentLiveBirds = max(0, $initialChicks - $totalMortality);
        $mortalityRate = $initialChicks > 0 ? round(($totalMortality / $initialChicks) * 100, 2) : 0;

        // تكلفة شراء الكتاكيت الأولية
        $chickCost = (float) ($cycle->chick_purchase_cost ?? 0);

        // فحص عمود نوع التكلفة في جدول poultry_broiler_costs
        $typeCol = Schema::hasColumn('poultry_broiler_costs', 'cost_type') ? 'cost_type' : 
                  (Schema::hasColumn('poultry_broiler_costs', 'type') ? 'type' : 
                  (Schema::hasColumn('poultry_broiler_costs', 'category') ? 'category' : null));

        if ($typeCol) {
            $feedCost = (float) ($cycle->costs()->where($typeCol, 'feed')->sum('amount') ?? 0);
            $otherCosts = (float) ($cycle->costs()->where($typeCol, '!=', 'feed')->sum('amount') ?? 0);
        } else {
            $feedCost = 0;
            $otherCosts = (float) ($cycle->costs()->sum('amount') ?? 0);
        }

        $totalCost = $chickCost + $feedCost + $otherCosts;

        // المبيعات والإيرادات
        $amountCol = Schema::hasColumn('poultry_broiler_sales', 'total_amount') ? 'total_amount' : 
                    (Schema::hasColumn('poultry_broiler_sales', 'total_price') ? 'total_price' : null);
        
        $totalRevenue = 0;
        if ($amountCol) {
            $totalRevenue = (float) ($cycle->sales()->sum($amountCol) ?? 0);
        } else {
            $totalRevenue = (float) ($cycle->sales()->get()->sum(fn($sale) => (float)($sale->quantity ?? 0) * (float)($sale->unit_price ?? 0)));
        }

        // فحص وجود عمود للوزن
        $weightCol = Schema::hasColumn('poultry_broiler_sales', 'weight_kg') ? 'weight_kg' : 
                    (Schema::hasColumn('poultry_broiler_sales', 'weight') ? 'weight' : 
                    (Schema::hasColumn('poultry_broiler_sales', 'total_weight') ? 'total_weight' : null));

        $totalSoldWeight = $weightCol ? (float) ($cycle->sales()->sum($weightCol) ?? 0) : 0;

        // استهلاك العلف
        $feedQtyCol = Schema::hasColumn('poultry_broiler_costs', 'quantity_kg') ? 'quantity_kg' : 
                     (Schema::hasColumn('poultry_broiler_costs', 'quantity') ? 'quantity' : null);

        $totalFeedKg = 0;
        if ($feedQtyCol) {
            $feedQuery = $cycle->costs();
            if ($typeCol) {
                $feedQuery->where($typeCol, 'feed');
            }
            $totalFeedKg = (float) ($feedQuery->sum($feedQtyCol) ?? 0);
        }

        $fcr = $totalSoldWeight > 0 ? round($totalFeedKg / $totalSoldWeight, 2) : 0;
        $netProfit = $totalRevenue - $totalCost;

        return [
            'initial_chicks'     => $initialChicks,
            'current_live_birds' => $currentLiveBirds,
            'total_mortality'    => $totalMortality,
            'mortality_rate'     => $mortalityRate,
            'total_feed_kg'      => $totalFeedKg,
            'total_sold_weight'  => $totalSoldWeight,
            'fcr'                => $fcr,
            'chick_cost'         => $chickCost,
            'feed_cost'          => $feedCost,
            'other_costs'        => $otherCosts,
            'total_cost'         => $totalCost,
            'total_revenue'      => $totalRevenue,
            'net_profit'         => $netProfit,
        ];
    }

    /**
     * ترحيل القيد المحاسبي لإغلاق دورة التسمين
     */
    public function recordBroilerCycleJournalEntry($cycle, int $userId): void
    {
        $journalClass = $this->getJournalEntryClass();
        if (! $journalClass) {
            return;
        }

        $metrics = $this->calculateBroilerCycleMetrics($cycle);

        DB::transaction(function () use ($cycle, $metrics, $userId, $journalClass) {
            $entryCode = 'JV-BC-' . $cycle->id . '-' . time();

            $journalEntry = $journalClass::create([
                'tenant_id'    => $cycle->tenant_id ?? (auth()->check() ? auth()->user()->tenant_id : null),
                'entry_no'     => $entryCode,
                'entry_number' => $entryCode,
                'entry_date'   => now(),
                'date'         => now(),
                'description'  => 'تسوية ختامية لدورة التسمين رقم #' . $cycle->id,
                'created_by'   => $userId,
                'status'       => 'posted',
            ]);

            if ($metrics['total_cost'] > 0 && method_exists($journalEntry, 'lines')) {
                $journalEntry->lines()->create([
                    'account_id'  => 5,
                    'debit'       => $metrics['total_cost'],
                    'credit'      => 0,
                    'description' => 'إجمالي تكاليف دورة التسمين #' . $cycle->id,
                ]);
            }

            if ($metrics['total_revenue'] > 0 && method_exists($journalEntry, 'lines')) {
                $journalEntry->lines()->create([
                    'account_id'  => 6,
                    'debit'       => 0,
                    'credit'      => $metrics['total_revenue'],
                    'description' => 'مبيعات دورة التسمين #' . $cycle->id,
                ]);
            }
        });
    }

    /**
     * حساب أرباح وخسائر دفعة تفقيس محددة
     */
    public function calculateBatchProfitLoss(PoultryHatcheryBatch $batch): array
    {
        $eggPurchaseCost = (float) ($batch->egg_purchase_cost ?? 0);
        $operationalCost = (float) ($batch->operational_cost ?? 0);
        $totalCost = $eggPurchaseCost + $operationalCost;

        $chicksHatched = (int) ($batch->chicks_hatched ?? 0);
        $salePricePerChick = (float) ($batch->chick_sale_price ?? 0);
        $totalRevenue = $chicksHatched * $salePricePerChick;

        $netProfit = $totalRevenue - $totalCost;
        $totalEggs = (int) ($batch->egg_quantity ?? 0);
        $hatchRate = $totalEggs > 0 ? round(($chicksHatched / $totalEggs) * 100, 2) : 0;

        return [
            'total_eggs'         => $totalEggs,
            'chicks_hatched'     => $chicksHatched,
            'hatch_rate_percent' => $hatchRate,
            'egg_purchase_cost'  => $eggPurchaseCost,
            'operational_cost'   => $operationalCost,
            'total_cost'         => $totalCost,
            'total_revenue'      => $totalRevenue,
            'net_profit'         => $netProfit,
            'cost_per_chick'     => $chicksHatched > 0 ? round($totalCost / $chicksHatched, 2) : 0,
        ];
    }

    /**
     * ترحيل القيد اليومي لدفعة التفقيس بعد اكتمالها
     */
    public function recordBatchJournalEntry(PoultryHatcheryBatch $batch, int $userId): void
    {
        $journalClass = $this->getJournalEntryClass();
        if (! $journalClass) {
            return;
        }

        $financials = $this->calculateBatchProfitLoss($batch);

        DB::transaction(function () use ($batch, $financials, $userId, $journalClass) {
            $entryCode = 'JV-HB-' . $batch->id . '-' . time();

            $journalEntry = $journalClass::create([
                'tenant_id'    => $batch->tenant_id ?? (auth()->check() ? auth()->user()->tenant_id : null),
                'entry_no'     => $entryCode,
                'entry_number' => $entryCode,
                'entry_date'   => now(),
                'date'         => now(),
                'description'  => 'تسوية أرباح/تكاليف دفعة التفقيس رقم #' . $batch->id,
                'created_by'   => $userId,
                'status'       => 'posted',
            ]);

            if ($financials['total_cost'] > 0 && method_exists($journalEntry, 'lines')) {
                $journalEntry->lines()->create([
                    'account_id'  => 1,
                    'debit'       => $financials['total_cost'],
                    'credit'      => 0,
                    'description' => 'تكلفة البيض المخصب والتشغيل للدفعة #' . $batch->id,
                ]);
            }

            if ($financials['total_revenue'] > 0 && method_exists($journalEntry, 'lines')) {
                $journalEntry->lines()->create([
                    'account_id'  => 2,
                    'debit'       => 0,
                    'credit'      => $financials['total_revenue'],
                    'description' => 'إيراد بيع كتاكيت الدفعة #' . $batch->id,
                ]);
            }
        });
    }

    /**
     * حساب أرباح وخسائر رحلات وتأجير السيارات
     */
    public function calculateRentalProfitLoss(?string $startDate = null, ?string $endDate = null, ?int $vehicleId = null): array
    {
        $query = PoultryVehicleRental::query();

        if ($vehicleId) {
            $query->where('vehicle_id', $vehicleId);
        }

        if ($startDate && $endDate) {
            $query->whereBetween('started_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay(),
            ]);
        }

        $rentals = $query->get();

        $totalRevenue = (float) $rentals->sum('rental_fee');
        $totalFuelCost = (float) $rentals->sum('fuel_cost');
        $totalDriverCommission = (float) $rentals->sum('driver_commission');
        $totalOtherExpenses = (float) $rentals->sum('other_expenses');

        $totalExpenses = $totalFuelCost + $totalDriverCommission + $totalOtherExpenses;
        $netProfit = $totalRevenue - $totalExpenses;

        return [
            'trips_count'             => $rentals->count(),
            'total_revenue'           => $totalRevenue,
            'total_fuel_cost'         => $totalFuelCost,
            'total_driver_commission' => $totalDriverCommission,
            'total_other_expenses'    => $totalOtherExpenses,
            'total_expenses'          => $totalExpenses,
            'net_profit'              => $netProfit,
            'profit_margin_percent'   => $totalRevenue > 0 ? round(($netProfit / $totalRevenue) * 100, 2) : 0,
        ];
    }

    /**
     * ترحيل القيد اليومي لرحلة تأجير سيارة
     */
    public function recordRentalJournalEntry(PoultryVehicleRental $rental, int $userId): void
    {
        $journalClass = $this->getJournalEntryClass();
        if (! $journalClass) {
            return;
        }

        DB::transaction(function () use ($rental, $userId, $journalClass) {
            $entryCode = 'JV-VR-' . $rental->id . '-' . time();
            $entryDate = $rental->started_at ?? now();

            $journalEntry = $journalClass::create([
                'tenant_id'    => $rental->tenant_id ?? (auth()->check() ? auth()->user()->tenant_id : null),
                'entry_no'     => $entryCode,
                'entry_number' => $entryCode,
                'entry_date'   => $entryDate,
                'date'         => $entryDate,
                'description'  => 'قيد إيرادات ومصروفات رحلة تأجير سيارة نقل #' . $rental->vehicle_id,
                'created_by'   => $userId,
                'status'       => 'posted',
            ]);

            if ($rental->rental_fee > 0 && method_exists($journalEntry, 'lines')) {
                $journalEntry->lines()->create([
                    'account_id'  => 3,
                    'debit'       => 0,
                    'credit'      => (float) $rental->rental_fee,
                    'description' => 'إيراد رحلة تأجير',
                ]);
            }

            $expenses = (float) $rental->fuel_cost + (float) $rental->driver_commission + (float) $rental->other_expenses;
            if ($expenses > 0 && method_exists($journalEntry, 'lines')) {
                $journalEntry->lines()->create([
                    'account_id'  => 4,
                    'debit'       => $expenses,
                    'credit'      => 0,
                    'description' => 'مصروفات رحلة تأجير (وقود/سائق/نثريات)',
                ]);
            }
        });
    }
}