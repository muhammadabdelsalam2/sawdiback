<?php

namespace App\Http\Controllers\CropsFeed;

use App\Http\Controllers\Controller;
use App\Http\Requests\CropsFeed\CropCostItemStoreRequest;
use App\Http\Requests\CropsFeed\CropGrowthStageStoreRequest;
use App\Http\Requests\CropsFeed\CropStoreRequest;
use App\Http\Requests\CropsFeed\CropUpdateRequest;
use App\Models\Crop;
use App\Models\CropCostItem;
use App\Models\CropGrowthStage;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Throwable;

class CropController extends Controller
{
    public function index(string $locale): View
    {
        $rows = Crop::query()
            ->withCount(['growthStages', 'costItems'])
            ->orderByDesc('id')
            ->paginate(15);

        return view('dashboard.crops_feed.crops.index', compact('rows'));
    }

    public function create(string $locale): View
    {
        return view('dashboard.crops_feed.crops.create');
    }

    public function store(CropStoreRequest $request, string $locale): RedirectResponse
    {
        $crop = Crop::query()->create($request->validated());

        return redirect()
            ->route('customer.crops-feed.crops.show', ['locale' => session('locale_full', 'en-SA'), 'crop' => $crop->id])
            ->with('success', 'Crop created successfully.');
    }

    public function show(string $locale, Crop $crop): View
    {
        $crop->load([
            'growthStages' => fn ($q) => $q->orderByDesc('recorded_on'),
            'costItems' => fn ($q) => $q->orderByDesc('cost_date'),
            'feedAllocations.feedType',
        ]);

        return view('dashboard.crops_feed.crops.show', compact('crop'));
    }

    public function edit(string $locale, Crop $crop): View
    {
        return view('dashboard.crops_feed.crops.edit', compact('crop'));
    }

    public function update(CropUpdateRequest $request, string $locale, Crop $crop): RedirectResponse
    {
        $crop->update($request->validated());

        return redirect()
            ->route('customer.crops-feed.crops.show', ['locale' => session('locale_full', 'en-SA'), 'crop' => $crop->id])
            ->with('success', 'Crop updated successfully.');
    }

    public function destroy(string $locale, Crop $crop): RedirectResponse
    {
        try {
            $crop->delete();
            return redirect()->route('customer.crops-feed.crops.index', ['locale' => session('locale_full', 'en-SA')])
                ->with('success', 'Crop deleted successfully.');
        } catch (Throwable $e) {
            return redirect()->back()->with('error', 'Crop cannot be deleted because it is in use.');
        }
    }

    public function storeGrowthStage(CropGrowthStageStoreRequest $request, string $locale): RedirectResponse
    {
        CropGrowthStage::query()->create($request->validated());

        return redirect()->back()->with('success', 'Growth stage recorded successfully.');
    }

    public function storeCostItem(CropCostItemStoreRequest $request, string $locale): RedirectResponse
    {
        CropCostItem::query()->create($request->validated());

        return redirect()->back()->with('success', 'Cost item recorded successfully.');
    }
}
