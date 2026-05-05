<?php

namespace App\Repositories;

use App\Models\Article;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ArticleRepository
{
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Article::query()
            ->latest('id')
            ->paginate($perPage);
    }

    public function findById(int $id): ?Article
    {
        return Article::query()->find($id);
    }
}
