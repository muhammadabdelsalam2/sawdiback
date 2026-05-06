<?php

namespace App\Repositories;

use App\Models\Content;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ContentRepository
{
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Content::query()
            ->latest('id')
            ->paginate($perPage);
    }

    public function findById(int $id): ?Content
    {
        return Content::query()->find($id);
    }

    public function create(array $data): Content
    {
        return Content::query()->create($data);
    }

    public function update(Content $content, array $data): Content
    {
        $content->update($data);

        return $content->refresh();
    }

    public function delete(Content $content): void
    {
        $content->delete();
    }
}
