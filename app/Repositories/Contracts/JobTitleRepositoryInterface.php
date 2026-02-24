<?php

namespace App\Repositories\Contracts;

use App\Models\JobTitle;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface JobTitleRepositoryInterface
{
    public function paginate(string $tenantId, int $perPage = 15): LengthAwarePaginator;
    public function create(array $data): JobTitle;
    public function update(JobTitle $jobTitle, array $data): JobTitle;
    public function delete(JobTitle $jobTitle): bool;
}
