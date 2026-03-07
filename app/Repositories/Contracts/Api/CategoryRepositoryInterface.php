<?php

namespace App\Repositories\Contracts\Api;

use App\DTOs\Api\CategoryDTO;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface CategoryRepositoryInterface
{

public function all(): LengthAwarePaginator;
    public function find(int $id): ?CategoryDTO;
    public function create(CategoryDTO $dto): CategoryDTO;
    public function update(int $id, CategoryDTO $dto): CategoryDTO;
    public function delete(int $id): bool;




}
