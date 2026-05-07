<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Http\Requests\Warehouse\InventoryCategoryStoreRequest;
use App\Http\Requests\Warehouse\InventoryCategoryUpdateRequest;
use App\Models\Category;
use App\Models\CategoryTranslation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class InventoryCategoryController extends Controller
{
    public function index(string $locale): View
    {
        $rows = $this->baseQuery()
            ->with(['translation'])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate(15);

        return view('dashboard.warehouse.categories.index', compact('rows'));
    }

    public function create(string $locale): View
    {
        return view('dashboard.warehouse.categories.create');
    }

    public function store(InventoryCategoryStoreRequest $request, string $locale): RedirectResponse
    {
        $data = $request->validated();

        $category = Category::query()->create([
            'tenant_id' => $this->tenantId(),
            'parent_id' => null,
            'code' => $data['code'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => (bool) ($data['is_active'] ?? true),
            'notes' => $data['notes'] ?? null,
        ]);

        $this->upsertTranslation($category->id, $data['name']);

        return redirect()
            ->route('customer.inventory.categories.index', ['locale' => session('locale_full', 'en-SA')])
            ->with('success', 'Category created successfully.');
    }

    public function edit(string $locale, Category $category): View
    {
        $this->assertTenant($category);
        $category->load('translation');

        return view('dashboard.warehouse.categories.edit', compact('category'));
    }

    public function update(InventoryCategoryUpdateRequest $request, string $locale, Category $category): RedirectResponse
    {
        $this->assertTenant($category);
        $data = $request->validated();

        $category->update([
            'code' => $data['code'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => (bool) ($data['is_active'] ?? true),
            'notes' => $data['notes'] ?? null,
        ]);

        $this->upsertTranslation($category->id, $data['name']);

        return redirect()
            ->route('customer.inventory.categories.index', ['locale' => session('locale_full', 'en-SA')])
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(string $locale, Category $category): RedirectResponse
    {
        $this->assertTenant($category);
        try {
            $category->delete();

            return redirect()
                ->route('customer.inventory.categories.index', ['locale' => session('locale_full', 'en-SA')])
                ->with('success', 'Category deleted successfully.');
        } catch (Throwable) {
            return redirect()->back()->with('error', 'Category cannot be deleted because it is in use.');
        }
    }

    private function baseQuery()
    {
        $tenantId = $this->tenantId();

        return Category::query()
            ->when($tenantId, fn ($q) => $q->where('tenant_id', $tenantId));
    }

    private function upsertTranslation(int $categoryId, string $name): void
    {
        $locale = app()->getLocale();
        $slug = Str::slug($name);

        if ($slug === '') {
            $slug = Str::slug($categoryId . '-' . $locale);
        }

        CategoryTranslation::query()->updateOrCreate(
            ['category_id' => $categoryId, 'locale' => $locale],
            [
                'name' => $name,
                'slug' => $slug,
                'description' => $name,
            ]
        );
    }

    private function tenantId(): ?string
    {
        return session('tenant_id') ?? auth()->user()?->tenant_id;
    }

    private function assertTenant(Category $category): void
    {
        $tenantId = $this->tenantId();

        if ($tenantId && $category->tenant_id !== $tenantId) {
            abort(404);
        }
    }
}
