<?php

namespace App\Repositories\Finance;

use App\Models\Finance\JournalEntry;
use App\Models\Finance\JournalEntryLine;
use App\Repositories\Contracts\Finance\JournalEntryRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class JournalEntryRepository implements JournalEntryRepositoryInterface
{
    public function paginate(string $tenantId, array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return JournalEntry::query()
            ->with(['lines.account'])
            ->where('tenant_id', $tenantId)
            ->when($filters['date_from'] ?? null, fn ($q, $v) => $q->whereDate('entry_date', '>=', $v))
            ->when($filters['date_to'] ?? null, fn ($q, $v) => $q->whereDate('entry_date', '<=', $v))
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function create(array $data, array $lines): JournalEntry
    {
        return DB::transaction(function () use ($data, $lines) {
            $entry = JournalEntry::query()->create($data);

            foreach ($lines as $line) {
                $entry->lines()->create($line);
            }

            return $entry->refresh()->load(['lines.account']);
        });
    }

    public function update(JournalEntry $entry, array $data, array $lines): JournalEntry
    {
        return DB::transaction(function () use ($entry, $data, $lines) {
            $entry->update($data);
            $entry->lines()->delete();

            foreach ($lines as $line) {
                $entry->lines()->create($line);
            }

            return $entry->refresh()->load(['lines.account']);
        });
    }

    public function findBySource(string $tenantId, string $sourceType, int|string $sourceId): ?JournalEntry
    {
        return JournalEntry::query()
            ->where('tenant_id', $tenantId)
            ->where('source_type', $sourceType)
            ->where('source_id', $sourceId)
            ->first();
    }

    public function deleteBySource(string $tenantId, string $sourceType, int|string $sourceId): bool
    {
        $entry = $this->findBySource($tenantId, $sourceType, $sourceId);

        if (!$entry) {
            return false;
        }

        return (bool) $entry->delete();
    }

    public function ledgerLines(string $tenantId, int $accountId, array $filters, int $perPage = 20): LengthAwarePaginator
    {
        return JournalEntryLine::query()
            ->with(['entry'])
            ->whereHas('entry', fn ($q) => $q->where('tenant_id', $tenantId))
            ->where('account_id', $accountId)
            ->when($filters['date_from'] ?? null, fn ($q, $v) => $q->whereHas('entry', fn ($e) => $e->whereDate('entry_date', '>=', $v)))
            ->when($filters['date_to'] ?? null, fn ($q, $v) => $q->whereHas('entry', fn ($e) => $e->whereDate('entry_date', '<=', $v)))
            ->orderBy('journal_entry_id')->orderBy('id')
            ->paginate($perPage)
            ->withQueryString();
    }
}
