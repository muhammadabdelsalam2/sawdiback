<?php

namespace App\Repositories\Contracts\Finance;

use App\Models\Finance\JournalEntry;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface JournalEntryRepositoryInterface
{
    public function paginate(string $tenantId, array $filters, int $perPage = 15): LengthAwarePaginator;
    public function create(array $data, array $lines): JournalEntry;
    public function update(JournalEntry $entry, array $data, array $lines): JournalEntry;
    public function findBySource(string $tenantId, string $sourceType, int|string $sourceId): ?JournalEntry;
    public function deleteBySource(string $tenantId, string $sourceType, int|string $sourceId): bool;
    public function ledgerLines(string $tenantId, int $accountId, array $filters, int $perPage = 20): LengthAwarePaginator;
}
