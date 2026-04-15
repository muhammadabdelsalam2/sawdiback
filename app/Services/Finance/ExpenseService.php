<?php

namespace App\Services\Finance;

use App\Models\Finance\Expense;
use App\Repositories\Contracts\Finance\ExpenseRepositoryInterface;
use Illuminate\Support\Facades\DB;

class ExpenseService
{
    public function __construct(
        private readonly ExpenseRepositoryInterface $expenses,
        private readonly JournalService $journal
    ) {}

    public function paginate(string $tenantId, array $filters)
    {
        return $this->expenses->paginate($tenantId, $filters);
    }

    public function create(string $tenantId, array $data): Expense
    {
        return DB::transaction(function () use ($tenantId, $data) {
            $expense = $this->expenses->create([
                'tenant_id' => $tenantId,
                ...$data,
            ]);

            $this->syncJournal($expense);

            return $expense;
        });
    }

    public function update(Expense $expense, array $data): Expense
    {
        return DB::transaction(function () use ($expense, $data) {
            $updated = $this->expenses->update($expense, $data);
            $this->syncJournal($updated);

            return $updated;
        });
    }

    public function delete(Expense $expense): bool
    {
        return DB::transaction(function () use ($expense) {
            $deleted = $this->expenses->delete($expense);
            $this->journal->deleteBySource((string) $expense->tenant_id, 'expense', (int) $expense->id);

            return $deleted;
        });
    }

    private function syncJournal(Expense $expense): void
    {
        if ($expense->status !== 'posted') {
            $this->journal->deleteBySource((string) $expense->tenant_id, 'expense', (int) $expense->id);
            return;
        }

        $amount = round((float) $expense->amount, 2);
        if ($amount <= 0) {
            return;
        }

        $this->journal->upsertBySource((string) $expense->tenant_id, 'expense', (int) $expense->id, [
            'entry_date' => $expense->expense_date?->format('Y-m-d') ?? now()->toDateString(),
            'description' => 'Expense ' . $expense->expense_no,
            'created_by' => $expense->created_by,
        ], [
            ['account_id' => $expense->expense_account_id, 'debit' => $amount, 'credit' => 0, 'memo' => 'Expense'],
            ['account_id' => $expense->payment_account_id, 'debit' => 0, 'credit' => $amount, 'memo' => 'Payment'],
        ]);
    }
}
