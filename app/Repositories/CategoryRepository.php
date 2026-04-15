<?php

namespace App\Repositories;

use App\DTOs\Api\CategoryDTO;
use App\Models\Category;
use App\Repositories\Contracts\Api\CategoryRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function all(): LengthAwarePaginator
    {
        return Category::query()
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    public function find(int $id): ?CategoryDTO
    {
        $category = Category::find($id);
        return $category ? new CategoryDTO(
            id: $category->id,
            name: $category->name,
            slug: $category->slug,
            image: $category->image,
            description: $category->description,
            is_active: $category->is_active,
        ) : null;
    }

    public function create(CategoryDTO $dto): CategoryDTO
    {
        $category = Category::create([
            'name' => $dto->name,
            'slug' => $dto->slug ?? \Str::slug($dto->name),
            'image' => $dto->image,
            'description' => $dto->description,
            'is_active' => $dto->is_active,
        ]);

        $dto->id = $category->id;
        return $dto;
    }

    public function update(int $id, CategoryDTO $dto): CategoryDTO
    {
        $category = Category::findOrFail($id);
        $category->update([
            'name' => $dto->name,
            'slug' => $dto->slug ?? \Str::slug($dto->name),
            'image' => $dto->image,
            'description' => $dto->description,
            'is_active' => $dto->is_active,
        ]);

        $dto->id = $category->id;
        return $dto;
    }

    public function delete(int $id): bool
    {
        $category = Category::findOrFail($id);
        return $category->delete();
    }
}