<?php

namespace App\Services\Finance;

use App\Models\Finance\JournalEntry;
use App\Repositories\Contracts\Finance\JournalEntryRepositoryInterface;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class JournalService
{
    public function __construct(
        private readonly JournalEntryRepositoryInterface $entries
    ) {}

    public function create(string $tenantId, array $data, array $lines): JournalEntry
    {
        $lines = $this->normalizeLines($lines);
        $this->ensureBalanced($lines);

        $payload = [
            'tenant_id' => $tenantId,
            'entry_no' => $data['entry_no'] ?? $this->generateEntryNo(),
            'entry_date' => $data['entry_date'],
            'description' => $data['description'] ?? null,
            'source_type' => $data['source_type'] ?? null,
            'source_id' => $data['source_id'] ?? null,
            'created_by' => $data['created_by'] ?? null,
        ];

        return $this->entries->create($payload, $lines);
    }

    public function upsertBySource(string $tenantId, string $sourceType, int|string $sourceId, array $data, array $lines): JournalEntry
    {
        $lines = $this->normalizeLines($lines);
        $this->ensureBalanced($lines);

        $existing = $this->entries->findBySource($tenantId, $sourceType, $sourceId);

        $payload = [
            'tenant_id' => $tenantId,
            'entry_no' => $existing?->entry_no ?? ($data['entry_no'] ?? $this->generateEntryNo()),
            'entry_date' => $data['entry_date'],
            'description' => $data['description'] ?? null,
            'source_type' => $sourceType,
            'source_id' => $sourceId,
            'created_by' => $data['created_by'] ?? null,
        ];

        if ($existing) {
            return $this->entries->update($existing, $payload, $lines);
        }

        return $this->entries->create($payload, $lines);
    }

    public function deleteBySource(string $tenantId, string $sourceType, int|string $sourceId): bool
    {
        return $this->entries->deleteBySource($tenantId, $sourceType, $sourceId);
    }

    private function normalizeLines(array $lines): array
    {
        $normalized = [];

        foreach ($lines as $line) {
            $debit = round((float) ($line['debit'] ?? 0), 2);
            $credit = round((float) ($line['credit'] ?? 0), 2);

            if ($debit <= 0 && $credit <= 0) {
                continue;
            }

            if ($debit > 0 && $credit > 0) {
                throw ValidationException::withMessages([
                    'lines' => __('finance.validation.messages.debit_credit_conflict'),
                ]);
            }

            $normalized[] = [
                'account_id' => (int) $line['account_id'],
                'debit' => $debit,
                'credit' => $credit,
                'memo' => $line['memo'] ?? null,
            ];
        }

        if (!count($normalized)) {
            throw ValidationException::withMessages([
                'lines' => __('finance.validation.messages.lines_required'),
            ]);
        }

        return $normalized;
    }

    private function ensureBalanced(array $lines): void
    {
        $totalDebit = round(array_sum(array_column($lines, 'debit')), 2);
        $totalCredit = round(array_sum(array_column($lines, 'credit')), 2);

        if ($totalDebit !== $totalCredit) {
            throw ValidationException::withMessages([
                'lines' => __('finance.validation.messages.unbalanced'),
            ]);
        }
    }

    private function generateEntryNo(): string
    {
        return 'JE-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
    }
}
