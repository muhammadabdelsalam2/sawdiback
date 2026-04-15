<?php

namespace App\Services\Finance;

use Illuminate\Support\Facades\DB;

class ProfitLossService
{
    public function report(string $tenantId, array $filters = []): array
    {
        $dateFrom = $filters['date_from'] ?? null;
        $dateTo = $filters['date_to'] ?? null;

        $revenues = $this->sumByType($tenantId, 'revenue', $dateFrom, $dateTo, true);
        $expenses = $this->sumByType($tenantId, 'expense', $dateFrom, $dateTo, false);

        $totalRevenue = array_sum(array_column($revenues, 'amount'));
        $totalExpenses = array_sum(array_column($expenses, 'amount'));

        return [
            'revenues' => $revenues,
            'expenses' => $expenses,
            'total_revenue' => round($totalRevenue, 2),
            'total_expenses' => round($totalExpenses, 2),
            'net_profit' => round($totalRevenue - $totalExpenses, 2),
        ];
    }

    private function sumByType(string $tenantId, string $type, ?string $dateFrom, ?string $dateTo, bool $creditMinusDebit): array
    {
        $query = DB::table('journal_entry_lines as l')
            ->join('journal_entries as e', 'e.id', '=', 'l.journal_entry_id')
            ->join('accounts as a', 'a.id', '=', 'l.account_id')
            ->where('e.tenant_id', $tenantId)
            ->where('a.type', $type)
            ->groupBy('a.id', 'a.code', 'a.name')
            ->orderBy('a.code');

        if ($dateFrom) {
            $query->whereDate('e.entry_date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('e.entry_date', '<=', $dateTo);
        }

        $rows = $query->selectRaw('a.id, a.code, a.name, SUM(l.debit) as total_debit, SUM(l.credit) as total_credit')->get();

        return $rows->map(function ($row) use ($creditMinusDebit) {
            $debit = (float) $row->total_debit;
            $credit = (float) $row->total_credit;
            $amount = $creditMinusDebit ? ($credit - $debit) : ($debit - $credit);

            return [
                'id' => $row->id,
                'code' => $row->code,
                'name' => $row->name,
                'amount' => round($amount, 2),
            ];
        })->toArray();
    }
}
