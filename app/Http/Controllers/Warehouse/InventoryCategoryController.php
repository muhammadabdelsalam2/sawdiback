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
<<<<<<< Updated upstream
            ->with(['translation'])
=======
            ->with(['translations'])
            ->orderBy('name_translations->' . $this->localeKey())
>>>>>>> Stashed changes
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

<<<<<<< Updated upstream
        $category = Category::query()->create([
            'tenant_id' => $this->tenantId(),
            'parent_id' => null,
            'code' => $data['code'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => (bool) ($data['is_active'] ?? true),
            'notes' => $data['notes'] ?? null,
        ]);
=======
        try {
            DB::transaction(function () use ($request, $data, $imagePath): void {
                $category = Category::query()->create([
                    'tenant_id' => $this->tenantId(),
                    'parent_id' => null,
                    'code' => $data['code'] ?? null,
                    'image' => $imagePath,
                    'sort_order' => $data['sort_order'] ?? 0,
                    'is_active' => $request->boolean('is_active', true),
                    'notes' => $data['notes'] ?? null,
                    'name_translations' => [$this->localeKey() => $data['name']],
                ]);
>>>>>>> Stashed changes

        $this->upsertTranslation($category->id, $data['name']);

        return redirect()
            ->route('customer.inventory.categories.index', ['locale' => session('locale_full', 'en-SA')])
            ->with('success', __('warehouse.messages.success.category_created'));
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

<<<<<<< Updated upstream
        $this->upsertTranslation($category->id, $data['name']);
=======
        try {
            DB::transaction(function () use ($request, $category, $data, $newImagePath): void {
                $translations = $category->name_translations ?? [];
                $translations[$this->localeKey()] = $data['name'];

                $categoryData = [
                    'code' => $data['code'] ?? null,
                    'sort_order' => $data['sort_order'] ?? 0,
                    'is_active' => $request->boolean('is_active'),
                    'notes' => $data['notes'] ?? null,
                    'name_translations' => $translations,
                ];

                if ($newImagePath) {
                    $categoryData['image'] = $newImagePath;
                }

                $category->update($categoryData);
                $this->upsertTranslation($category->id, $data['name']);
            });
        } catch (Throwable $exception) {
            $this->deleteImage($newImagePath);

            throw $exception;
        }

        if ($newImagePath) {
            $this->deleteImage($oldImage);
        }
>>>>>>> Stashed changes

        return redirect()
            ->route('customer.inventory.categories.index', ['locale' => session('locale_full', 'en-SA')])
            ->with('success', __('warehouse.messages.success.category_updated'));
    }

    public function destroy(string $locale, Category $category): RedirectResponse
    {
        $this->assertTenant($category);
        try {
            $category->delete();

            return redirect()
                ->route('customer.inventory.categories.index', ['locale' => session('locale_full', 'en-SA')])
                ->with('success', __('warehouse.messages.success.category_deleted'));
        } catch (Throwable) {
            return redirect()->back()->with('error', __('warehouse.messages.error.category_in_use'));
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
