<?php

namespace App\Services\Finance;

use Illuminate\Support\Facades\DB;

class FinanceDashboardService
{
    public function summary(string $tenantId, array $filters = []): array
    {
        $dateFrom = $filters['date_from'] ?? null;
        $dateTo = $filters['date_to'] ?? null;

        $revenue = $this->sumByType($tenantId, 'revenue', $dateFrom, $dateTo, true);
        $expenses = $this->sumByType($tenantId, 'expense', $dateFrom, $dateTo, false);

        return [
            'total_revenue' => $revenue,
            'total_expenses' => $expenses,
            'net_profit' => round($revenue - $expenses, 2),
        ];
    }

    private function sumByType(string $tenantId, string $type, ?string $dateFrom, ?string $dateTo, bool $creditMinusDebit): float
    {
        $query = DB::table('journal_entry_lines as l')
            ->join('journal_entries as e', 'e.id', '=', 'l.journal_entry_id')
            ->join('accounts as a', 'a.id', '=', 'l.account_id')
            ->where('e.tenant_id', $tenantId)
            ->where('a.type', $type);

        if ($dateFrom) {
            $query->whereDate('e.entry_date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('e.entry_date', '<=', $dateTo);
        }

        $row = $query->selectRaw('SUM(l.debit) as total_debit, SUM(l.credit) as total_credit')->first();

        $debit = (float) ($row->total_debit ?? 0);
        $credit = (float) ($row->total_credit ?? 0);

        return $creditMinusDebit
            ? round($credit - $debit, 2)
            : round($debit - $credit, 2);
    }
}
