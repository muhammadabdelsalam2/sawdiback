<?php

namespace App\Repositories;

use App\DTOs\Api\CategoryDTO;
use App\Models\Category;
use App\Models\CategoryTranslation;
use App\Repositories\Contracts\Api\CategoryRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function all(): LengthAwarePaginator
    {
        return $this->baseQuery()
            ->with(['translations'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    public function find(int $id): ?CategoryDTO
    {
        $category = $this->baseQuery()
            ->with(['translations'])
            ->find($id);

        $translation = $category?->translations
            ->firstWhere('locale', $this->localeKey())
            ?? $category?->translations->firstWhere('locale', 'en')
            ?? $category?->translations->first();

        return $category ? new CategoryDTO(
            id: $category->id,
            name: $category->name,
            slug: $translation?->slug,
            image: $category->image,
            description: $translation?->description ?? $category->notes,
            is_active: $category->is_active,
        ) : null;
    }

    public function create(CategoryDTO $dto): CategoryDTO
    {
        $categoryData = [
            'image' => $dto->image,
            'is_active' => $dto->is_active,
            'notes' => $dto->description,
        ];

        if ($tenantId = $this->tenantId()) {
            $categoryData['tenant_id'] = $tenantId;
        }

        $category = DB::transaction(function () use ($categoryData, $dto): Category {
            $category = Category::create($categoryData);
            $this->upsertTranslation($category->id, $dto);

            return $category;
        });

        $dto->id = $category->id;
        return $dto;
    }

    public function update(int $id, CategoryDTO $dto): CategoryDTO
    {
        $category = $this->baseQuery()->findOrFail($id);

        $categoryData = [
            'is_active' => $dto->is_active,
            'notes' => $dto->description,
        ];

        $oldImage = $category->image;

        if ($dto->image !== null) {
            $categoryData['image'] = $dto->image;
        }

        DB::transaction(function () use ($category, $categoryData, $dto): void {
            $category->update($categoryData);
            $this->upsertTranslation($category->id, $dto);
        });

        if ($dto->image !== null && $oldImage && $oldImage !== $dto->image && ! filter_var($oldImage, FILTER_VALIDATE_URL)) {
            Storage::disk('public')->delete($oldImage);
        }

        $dto->id = $category->id;
        return $dto;
    }

    public function delete(int $id): bool
    {
        $category = $this->baseQuery()->findOrFail($id);
        $image = $category->image;
        $deleted = (bool) $category->delete();

        if ($deleted && $image && ! filter_var($image, FILTER_VALIDATE_URL)) {
            Storage::disk('public')->delete($image);
        }

        return $deleted;
    }

    private function baseQuery()
    {
        return Category::query()
            ->when($this->tenantId(), fn ($query, $tenantId) => $query->where('tenant_id', $tenantId));
    }

    private function upsertTranslation(int $categoryId, CategoryDTO $dto): void
    {
        $slug = $dto->slug ?: Str::slug($dto->name);

        if ($slug === '') {
            $slug = Str::slug($categoryId . '-' . $this->localeKey());
        }

        CategoryTranslation::query()->updateOrCreate(
            ['category_id' => $categoryId, 'locale' => $this->localeKey()],
            [
                'name' => $dto->name,
                'slug' => $slug,
                'description' => $dto->description ?? $dto->name,
            ]
        );
    }

    private function tenantId(): ?string
    {
        return session('tenant_id') ?? auth()->user()?->tenant_id;
    }

    private function localeKey(): string
    {
        return substr(app()->getLocale(), 0, 2);
    }
}
