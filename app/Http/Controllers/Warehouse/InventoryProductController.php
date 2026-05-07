<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Http\Requests\Warehouse\InventoryProductStoreRequest;
use App\Http\Requests\Warehouse\InventoryProductUpdateRequest;
use App\Models\Category;
use App\Models\Farmer;
use App\Models\InventoryProduct;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Throwable;

class InventoryProductController extends Controller
{
    public function index(string $locale): View
    {
        $rows = InventoryProduct::query()
            ->with(['categoryRelation.translations'])
            ->orderBy('name')
            ->paginate(15);

        // إحصائيات للداشبورد
        $totalProducts = InventoryProduct::count();
        $activeProducts = InventoryProduct::where('is_active', true)->count();
        $lowStock = InventoryProduct::where('low_stock_threshold', '>', 0)->count();
        $bestSelling = InventoryProduct::where('is_best_selling', true)->count();

        return view('dashboard.warehouse.products.index', compact(
            'rows',
            'totalProducts',
            'activeProducts',
            'lowStock',
            'bestSelling'
        ));
    }

    public function create(string $locale): View
    {
        $categories = $this->categoriesForTenant();
        $farmers = $this->farmersForTenant();

        return view('dashboard.warehouse.products.create', compact('categories', 'farmers'));
    }

    public function store(InventoryProductStoreRequest $request, string $locale): RedirectResponse
    {
        $data = $request->validated();
        $category = $this->findCategoryForTenant($data['category_id']);
        // $data['category'] = $category->code;
        $data['tenant_id'] = $category->tenant_id ?? $this->tenantId();
        $data = $this->prepareLocalizedFields($request, $data);

        $data['is_active'] = $request->boolean('is_active');
        $data['track_expiry'] = $request->boolean('track_expiry');
        $data['is_best_selling'] = $request->boolean('is_best_selling');
        $data['farmer_id'] = $request->input('farmer_id');
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('inventory/products', 'public');
        }

        InventoryProduct::query()->create($data);

        return redirect()
            ->route('customer.inventory.products.index', ['locale' => session('locale_full', 'en-SA')])
            ->with('success', __('warehouse.messages.success.product_created'));
    }

    public function edit(string $locale, InventoryProduct $product): View
    {
        $categories = $this->categoriesForTenant();
        $farmers = $this->farmersForTenant();

        return view(
            'dashboard.warehouse.products.edit',
            compact('product', 'categories', 'farmers')
        );
    }

    public function update(InventoryProductUpdateRequest $request, string $locale, InventoryProduct $product): RedirectResponse
    {
        $data = $request->validated();

        $category = $this->findCategoryForTenant($data['category_id']);
        //$data['category'] = $category->code;
        $data['tenant_id'] = $product->tenant_id ?: ($category->tenant_id ?? $this->tenantId());
        $data = $this->prepareLocalizedFields($request, $data, $product);

        $data['is_active'] = $request->boolean('is_active');
        $data['track_expiry'] = $request->boolean('track_expiry');
        $data['is_best_selling'] = $request->boolean('is_best_selling');

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('inventory/products', 'public');
        }

        $product->update($data);

        return redirect()
            ->route('customer.inventory.products.index', ['locale' => session('locale_full', 'en-SA')])
            ->with('success', __('warehouse.messages.success.product_updated'));
    }

    public function destroy(string $locale, InventoryProduct $product): RedirectResponse
    {
        try {
            $product->delete();

            return redirect()
                ->route('customer.inventory.products.index', ['locale' => session('locale_full', 'en-SA')])
                ->with('success', __('warehouse.messages.success.product_deleted'));
        } catch (Throwable) {
            return redirect()->back()->with('error', __('warehouse.messages.error.product_in_use'));
        }
    }
    private function farmersForTenant()
    {
        $tenantId = $this->tenantId();

        return Farmer::query()
            ->orderBy('name')
            ->get();
    }
    private function categoriesForTenant()
    {
        $tenantId = $this->tenantId();

        return Category::query()->with(['translations'])
            ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }

    private function findCategoryForTenant(int $categoryId): Category
    {
        $tenantId = $this->tenantId();

        return Category::query()
            ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->findOrFail($categoryId);
    }

    private function tenantId(): ?string
    {
        return session('tenant_id') ?? auth()->user()?->tenant_id;
    }

    private function prepareLocalizedFields($request, array $data, ?InventoryProduct $product = null): array
    {
        $existingTitle = $this->normalizeTranslations($product?->getRawOriginal('title'), $product?->name);
        $existingDescription = $this->normalizeTranslations($product?->getRawOriginal('description'), $product?->notes);
        $nameFallback = $data['name'] ?? $product?->name;

        $data['title'] = [
            'ar' => $request->input('title_ar') ?: ($existingTitle['ar'] ?? $nameFallback),
            'en' => $request->input('title_en') ?: ($existingTitle['en'] ?? $nameFallback),
        ];

        $descriptionFallback = $data['notes']
            ?? $product?->notes
            ?? $existingDescription['en']
            ?? $existingDescription['ar']
            ?? null;

        $data['description'] = [
            'ar' => $request->input('description_ar') ?: ($existingDescription['ar'] ?? $descriptionFallback),
            'en' => $request->input('description_en') ?: ($existingDescription['en'] ?? $descriptionFallback),
        ];

        return $data;
    }

    private function normalizeTranslations(mixed $value, ?string $fallback = null): array
    {
        if (is_array($value)) {
            return $value;
        }

        if (is_string($value) && $value !== '') {
            $decodedValue = json_decode($value, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($decodedValue)) {
                return $decodedValue;
            }

            return [
                'ar' => $value,
                'en' => $value,
            ];
        }

        return [
            'ar' => $fallback,
            'en' => $fallback,
        ];
    }
}
