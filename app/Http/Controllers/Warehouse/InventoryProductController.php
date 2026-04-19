<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Http\Requests\Warehouse\InventoryProductStoreRequest;
use App\Http\Requests\Warehouse\InventoryProductUpdateRequest;
use App\Models\Category;
use App\Models\InventoryProduct;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Throwable;

class InventoryProductController extends Controller
{
public function index(string $locale): View
{
    $rows = InventoryProduct::query()
        ->with(['category.translation'])
        ->orderBy('name')
        ->paginate(15);

    // إحصائيات للداشبورد
    $totalProducts = InventoryProduct::count();
    $activeProducts = InventoryProduct::where('is_active', true)->count();
    $lowStock = InventoryProduct::whereColumn('available_quantity', '<=', 'low_stock_threshold')->count();
    $bestSelling = InventoryProduct::where('is_best_selling', true)->count();

    return view('dashboard.warehouse.products.index', compact(
        'rows', 'totalProducts', 'activeProducts', 'lowStock', 'bestSelling'
    ));
}

    public function create(string $locale): View
    {
        $categories = $this->categoriesForTenant();

        return view('dashboard.warehouse.products.create', compact('categories'));
    }

public function store(InventoryProductStoreRequest $request, string $locale): RedirectResponse
{
    $data = $request->validated();

    $category = $this->findCategoryForTenant($data['category_id']);
    // $data['category'] = $category->code;
    $data['tenant_id'] = $category->tenant_id ?? $this->tenantId();

    $data['is_active'] = $request->boolean('is_active');
    $data['track_expiry'] = $request->boolean('track_expiry');
    $data['is_best_selling'] = $request->boolean('is_best_selling');

    if ($request->hasFile('image')) {
        $data['image'] = $request->file('image')->store('inventory/products', 'public');
    }

    InventoryProduct::query()->create($data);

    return redirect()
        ->route('customer.inventory.products.index', ['locale' => session('locale_full', 'en-SA')])
        ->with('success', 'Product created successfully.');
}

    public function edit(string $locale, InventoryProduct $product): View
    {
        $categories = $this->categoriesForTenant();

        return view('dashboard.warehouse.products.edit', compact('product', 'categories'));
    }

    public function update(InventoryProductUpdateRequest $request, string $locale, InventoryProduct $product): RedirectResponse
    {
        $data = $request->validated();

        $category = $this->findCategoryForTenant($data['category_id']);
        //$data['category'] = $category->code;
        $data['tenant_id'] = $product->tenant_id ?: ($category->tenant_id ?? $this->tenantId());

        $data['is_active'] = $request->boolean('is_active');
        $data['track_expiry'] = $request->boolean('track_expiry');
        $data['is_best_selling'] = $request->boolean('is_best_selling');

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('inventory/products', 'public');
        }

        $product->update($data);

        return redirect()
            ->route('customer.inventory.products.index', ['locale' => session('locale_full', 'en-SA')])
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(string $locale, InventoryProduct $product): RedirectResponse
    {
        try {
            $product->delete();

            return redirect()
                ->route('customer.inventory.products.index', ['locale' => session('locale_full', 'en-SA')])
                ->with('success', 'Product deleted successfully.');
        } catch (Throwable) {
            return redirect()->back()->with('error', 'Product cannot be deleted because it is in use.');
        }
    }

    private function categoriesForTenant()
    {
        $tenantId = $this->tenantId();

        return Category::query()
            ->when($tenantId, fn ($q) => $q->where('tenant_id', $tenantId))
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }

    private function findCategoryForTenant(int $categoryId): Category
    {
        $tenantId = $this->tenantId();

        return Category::query()
            ->when($tenantId, fn ($q) => $q->where('tenant_id', $tenantId))
            ->findOrFail($categoryId);
    }

    private function tenantId(): ?string
    {
        return session('tenant_id') ?? auth()->user()?->tenant_id;
    }
}
