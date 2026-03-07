<?php

namespace App\Services\API;

use App\DTOs\Api\CategoryDTO;
use App\Repositories\CategoryRepository;

class CategoryService
{
    public function __construct(
        protected CategoryRepository $categoryRepository
    ) {
    }

    public function all()
    {
        return $this->categoryRepository->all();
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