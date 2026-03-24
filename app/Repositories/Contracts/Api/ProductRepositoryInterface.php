<?php
namespace App\Repositories\Contracts\Api;

use App\Models\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface ProductRepositoryInterface
{
    public function all(array $filters,int $perPage = 15): LengthAwarePaginator;

    public function byCategory(Category $category, int $perPage = 15): LengthAwarePaginator;

    public function search(string $query, int $perPage = 15): LengthAwarePaginator;



}