<?php

namespace App\Repositories\Finance;

use App\Models\Finance\Expense;
use App\Repositories\Contracts\Finance\ExpenseRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ExpenseRepository implements ExpenseRepositoryInterface
{
    public function paginate(string $tenantId, array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return Expense::query()
            ->with(['expenseAccount', 'paymentAccount'])
            ->where('tenant_id', $tenantId)
            ->when($filters['date_from'] ?? null, fn ($q, $v) => $q->whereDate('expense_date', '>=', $v))
            ->when($filters['date_to'] ?? null, fn ($q, $v) => $q->whereDate('expense_date', '<=', $v))
            ->when($filters['status'] ?? null, fn ($q, $v) => $q->where('status', $v))
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function create(array $data): Expense
    {
        return Expense::query()->create($data);
    }

    public function update(Expense $expense, array $data): Expense
    {
        $expense->update($data);

        return $expense->refresh();
    }

    public function delete(Expense $expense): bool
    {
        return (bool) $expense->delete();
    }
}
