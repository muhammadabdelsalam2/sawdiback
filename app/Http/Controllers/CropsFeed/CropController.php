<?php

namespace App\Http\Controllers\CropsFeed;

use App\Http\Controllers\Controller;
use App\Http\Requests\CropsFeed\CropCostItemStoreRequest;
use App\Http\Requests\CropsFeed\CropGrowthStageStoreRequest;
use App\Http\Requests\CropsFeed\CropMaterialUsageStoreRequest;
use App\Http\Requests\CropsFeed\CropStoreRequest;
use App\Http\Requests\CropsFeed\CropUpdateRequest;
use App\Models\Crop;
use App\Models\CropCostItem;
use App\Models\CropGrowthStage;
use App\Models\CropMaterialUsage;
use App\Models\Farm;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Throwable;

class CropController extends Controller
{
    public function index(string $locale): View
    {
        $rows = Crop::query()
            ->with('farm')
            ->withCount(['growthStages', 'costItems'])
            ->orderBy('name_translations->' . $this->localeKey())
            ->orderByDesc('id')
            ->paginate(15);

        return view('dashboard.crops_feed.crops.index', compact('rows'));
    }

    public function create(string $locale): View
    {
        $farms = Farm::query()->orderBy('name')->get();

        return view('dashboard.crops_feed.crops.create', compact('farms'));
    }

    public function store(CropStoreRequest $request, string $locale): RedirectResponse
    {
        $data = $request->validated();
        $data = $this->withCropDefaults($data);
        $data['name_translations'] = [$this->localeKey() => $data['name']];

        $crop = Crop::query()->create($data);

        return redirect()
            ->route('customer.crops-feed.crops.show', ['locale' => session('locale_full', 'en-SA'), 'crop' => $crop->id])
            ->with('success', __('crops_feed.messages.success.crop_created'));
    }

    public function show(string $locale, Crop $crop): View
    {
        $crop->load([
            'growthStages' => fn ($q) => $q->orderByDesc('recorded_on'),
            'costItems' => fn ($q) => $q->orderByDesc('cost_date'),
            'materialUsages' => fn ($q) => $q->orderByDesc('used_on'),
            'feedAllocations.feedType',
            'farm',
        ]);

        return view('dashboard.crops_feed.crops.show', compact('crop'));
    }

    public function edit(string $locale, Crop $crop): View
    {
        $farms = Farm::query()->orderBy('name')->get();

        return view('dashboard.crops_feed.crops.edit', compact('crop', 'farms'));
    }

    public function update(CropUpdateRequest $request, string $locale, Crop $crop): RedirectResponse
    {
        $data = $request->validated();
        $data = $this->withCropDefaults($data);
        $translations = $crop->name_translations ?? [];
        $translations[$this->localeKey()] = $data['name'];
        $data['name_translations'] = $translations;

        $crop->update($data);

        return redirect()
            ->route('customer.crops-feed.crops.show', ['locale' => session('locale_full', 'en-SA'), 'crop' => $crop->id])
            ->with('success', __('crops_feed.messages.success.crop_updated'));
    }

    public function destroy(string $locale, Crop $crop): RedirectResponse
    {
        try {
            $crop->delete();
            return redirect()->route('customer.crops-feed.crops.index', ['locale' => session('locale_full', 'en-SA')])
                ->with('success', __('crops_feed.messages.success.crop_deleted'));
        } catch (Throwable $e) {
            return redirect()->back()->with('error', __('crops_feed.messages.error.crop_in_use'));
        }
    }

    public function storeGrowthStage(CropGrowthStageStoreRequest $request, string $locale): RedirectResponse
    {
        CropGrowthStage::query()->create($request->validated());

        return redirect()->back()->with('success', __('crops_feed.messages.success.growth_stage_recorded'));
    }

    public function storeCostItem(CropCostItemStoreRequest $request, string $locale): RedirectResponse
    {
        CropCostItem::query()->create($request->validated());

        return redirect()->back()->with('success', __('crops_feed.messages.success.cost_item_recorded'));
    }

    public function storeMaterialUsage(CropMaterialUsageStoreRequest $request, string $locale): RedirectResponse
    {
        $data = $request->validated();
        $data['amount'] = $data['amount'] ?? 0;

        CropMaterialUsage::query()->create($data);

        return redirect()->back()->with('success', __('crops_feed.messages.success.material_usage_recorded'));
    }

    private function localeKey(): string
    {
        return substr(app()->getLocale(), 0, 2);
    }

    private function withCropDefaults(array $data): array
    {
        foreach (['wasted_tons', 'water_cost', 'labor_cost'] as $field) {
            $data[$field] = $data[$field] ?? 0;
        }

        return $data;
    }
}
