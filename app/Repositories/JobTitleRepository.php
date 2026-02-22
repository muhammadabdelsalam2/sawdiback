<?php

namespace App\Repositories;

use App\Models\JobTitle;
use App\Repositories\Contracts\JobTitleRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class JobTitleRepository implements JobTitleRepositoryInterface
{
    public function paginate(string $tenantId, int $perPage = 15): LengthAwarePaginator
    {
        return JobTitle::query()
            ->where('tenant_id', $tenantId)
            ->orderBy('id', 'desc')
            ->paginate($perPage);
    }

    public function create(array $data): JobTitle
    {
        return JobTitle::create($data);
    }

    public function update(JobTitle $jobTitle, array $data): JobTitle
    {
        $jobTitle->update($data);
        return $jobTitle;
    }

    public function delete(JobTitle $jobTitle): bool
    {
        return (bool) $jobTitle->delete();
    }
}
