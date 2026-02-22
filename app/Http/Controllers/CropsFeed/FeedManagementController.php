<?php

namespace App\Http\Controllers\CropsFeed;

use App\Http\Controllers\Controller;
use App\Http\Requests\CropsFeed\CropFeedAllocationStoreRequest;
use App\Http\Requests\CropsFeed\FeedConsumptionStoreRequest;
use App\Http\Requests\CropsFeed\FeedReportRequest;
use App\Http\Requests\CropsFeed\FeedStockMovementStoreRequest;
use App\Models\Crop;
use App\Models\FeedConsumption;
use App\Models\FeedType;
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
        $feedTypes = FeedType::query()->orderBy('name')->get();
        $crops = Crop::query()->orderByDesc('id')->get();
        $animals = \App\Models\LivestockAnimal::query()->orderBy('tag_number')->get();
        $recentConsumptions = FeedConsumption::query()->with(['feedType', 'animal'])->orderByDesc('id')->limit(20)->get();

        $stocks = $feedTypes->map(function (FeedType $feedType) {
            $onHand = $this->feedStockService->stockOnHand($feedType->id);
            return [
                'feedType' => $feedType,
                'stock_on_hand' => $onHand,
                'is_low_stock' => $onHand <= (float) $feedType->low_stock_threshold,
            ];
        });

        return view('dashboard.crops_feed.feed.index', compact('feedTypes', 'stocks', 'recentConsumptions', 'animals', 'crops'));
    }

    public function storeStockMovement(FeedStockMovementStoreRequest $request, string $locale): RedirectResponse
    {
        try {
            $this->recordFeedStockMovementService->execute($request->validated());
            return redirect()->back()->with('success', 'Feed stock movement recorded successfully.');
        } catch (RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function storeConsumption(FeedConsumptionStoreRequest $request, string $locale): RedirectResponse
    {
        try {
            $this->recordFeedConsumptionService->execute($request->validated());
            return redirect()->back()->with('success', 'Feed consumption recorded successfully.');
        } catch (RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function storeCropAllocation(CropFeedAllocationStoreRequest $request, string $locale): RedirectResponse
    {
        try {
            $this->allocateCropToFeedService->execute($request->validated());
            return redirect()->back()->with('success', 'Crop has been allocated to feed stock successfully.');
        } catch (RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function reports(FeedReportRequest $request, string $locale): View
    {
        $month = $request->validated('month') ?: now()->format('Y-m');
        $start = now()->setDate((int) substr($month, 0, 4), (int) substr($month, 5, 2), 1)->startOfMonth();
        $end = (clone $start)->endOfMonth();

        $monthlyFeedCost = (float) FeedConsumption::query()
            ->whereBetween('consumption_date', [$start->toDateString(), $end->toDateString()])
            ->sum('total_cost');

        $costPerAnimal = FeedConsumption::query()
            ->selectRaw('animal_id, SUM(total_cost) as total_cost')
            ->whereNotNull('animal_id')
            ->whereBetween('consumption_date', [$start->toDateString(), $end->toDateString()])
            ->groupBy('animal_id')
            ->with('animal')
            ->get();

        $feedTypes = FeedType::query()->orderBy('name')->get();
        $lowStockRows = $feedTypes->filter(function (FeedType $feedType) {
            $stock = $this->feedStockService->stockOnHand($feedType->id);
            return $stock <= (float) $feedType->low_stock_threshold;
        })->map(function (FeedType $feedType) {
            return [
                'feedType' => $feedType,
                'stock_on_hand' => $this->feedStockService->stockOnHand($feedType->id),
            ];
        });

        $farmFeedProduction = (float) Crop::query()->sum('available_for_feed_tons');
        $farmFeedNeed = (float) FeedConsumption::query()
            ->whereBetween('consumption_date', [$start->toDateString(), $end->toDateString()])
            ->sum('quantity');

        return view('dashboard.crops_feed.reports.index', compact(
            'month',
            'monthlyFeedCost',
            'costPerAnimal',
            'lowStockRows',
            'farmFeedProduction',
            'farmFeedNeed'
        ));
    }
}
