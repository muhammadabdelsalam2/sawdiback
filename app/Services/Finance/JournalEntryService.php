<?php

namespace App\Services\Finance;

use App\Models\Finance\JournalEntry;
use App\Repositories\Contracts\Finance\JournalEntryRepositoryInterface;

class JournalEntryService
{
    public function __construct(
        private readonly JournalEntryRepositoryInterface $entries,
        private readonly JournalService $journal
    ) {}

    public function paginate(string $tenantId, array $filters)
    {
        return $this->entries->paginate($tenantId, $filters);
    }

    public function create(string $tenantId, array $data): JournalEntry
    {
        return $this->journal->create($tenantId, $data, $data['lines'] ?? []);
    }
}
