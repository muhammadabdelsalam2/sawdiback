<?php

namespace App\Services\Finance;

use App\Models\Finance\Account;
use App\Repositories\Contracts\Finance\JournalEntryRepositoryInterface;
use Illuminate\Support\Facades\DB;

class LedgerService
{
    public function __construct(
        private readonly JournalEntryRepositoryInterface $entries
    ) {}

    public function ledger(Account $account, array $filters)
    {
        $tenantId = (string) $account->tenant_id;
        $lines = $this->entries->ledgerLines($tenantId, (int) $account->id, $filters);

        $opening = $this->openingBalance($account, $filters);
        $running = $opening;

        $lines->getCollection()->transform(function ($line) use ($account, &$running) {
            $running = $this->applyMovement($account, $running, (float) $line->debit, (float) $line->credit);
            $line->running_balance = $running;
            return $line;
        });

        return [
            'lines' => $lines,
            'opening_balance' => $opening,
            'closing_balance' => $running,
        ];
    }

    private function openingBalance(Account $account, array $filters): float
    {
        $tenantId = (string) $account->tenant_id;
        $dateFrom = $filters['date_from'] ?? null;

        if (!$dateFrom) {
            return 0.0;
        }

        $row = DB::table('journal_entry_lines as l')
            ->join('journal_entries as e', 'e.id', '=', 'l.journal_entry_id')
            ->where('e.tenant_id', $tenantId)
            ->where('l.account_id', $account->id)
            ->whereDate('e.entry_date', '<', $dateFrom)
            ->selectRaw('SUM(l.debit) as total_debit, SUM(l.credit) as total_credit')
            ->first();

        $debit = (float) ($row->total_debit ?? 0);
        $credit = (float) ($row->total_credit ?? 0);

        return $this->applyMovement($account, 0.0, $debit, $credit);
    }

    private function applyMovement(Account $account, float $balance, float $debit, float $credit): float
    {
        $normalDebit = in_array($account->type, ['asset', 'expense'], true);

        $delta = $normalDebit
            ? ($debit - $credit)
            : ($credit - $debit);

        return round($balance + $delta, 2);
    }
}
