<?php

namespace App\Services\API;

use App\DTOs\Api\CategoryDTO;
use App\Repositories\CategoryRepository;
use App\Http\Resources\CategoryResource;
use App\Support\ServiceResult;


class CategoryService
{
    public function __construct(
        protected CategoryRepository $categoryRepository
    ) {
    }

public function all()
{
    $categories = $this->categoryRepository->all(); // LengthAwarePaginator
    $categoriesData = CategoryResource::collection($categories);

    // Use separate key for resource name
    $resourceName = __('ecommerce.category.name'); 

    // Use resource name in the success message
    $message = __('ecommerce.category.success', ['resource' => $resourceName]);

    return ServiceResult::success(
        data: $categoriesData,
        message: $message,
        code: 200
    );
}

    public function find(int $id): ?CategoryDTO
    {
        return $this->categoryRepository->find($id);
    }

    public function create(CategoryDTO $dto): CategoryDTO
    {
        return $this->categoryRepository->create($dto);
    }

    public function update(int $id, CategoryDTO $dto): CategoryDTO
    {
        return $this->categoryRepository->update($id, $dto);
    }

    public function delete(int $id): bool
    {
        return $this->categoryRepository->delete($id);
    }
}