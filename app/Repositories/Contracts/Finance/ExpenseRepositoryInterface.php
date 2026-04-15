<?php

namespace App\Repositories\Contracts\Finance;

use App\Models\Finance\Expense;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ExpenseRepositoryInterface
{
    public function paginate(string $tenantId, array $filters, int $perPage = 15): LengthAwarePaginator;
    public function create(array $data): Expense;
    public function update(Expense $expense, array $data): Expense;
    public function delete(Expense $expense): bool;
}
