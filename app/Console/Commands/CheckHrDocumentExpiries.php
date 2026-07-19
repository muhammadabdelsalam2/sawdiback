<?php

namespace App\Console\Commands;

use App\Services\HR\HrDocumentAlertService;
use Illuminate\Console\Command;

class CheckHrDocumentExpiries extends Command
{
    protected $signature = 'hr:check-document-expiries {--days=}';
    protected $description = 'Check employees with passports or iqamas expiring soon.';

    public function handle(HrDocumentAlertService $alerts): int
    {
        $days = (int) ($this->option('days') ?? config('hr.document_expiry_alert_days', 30));
        $rows = $alerts->expiringDocuments($days);

        if ($rows->isEmpty()) {
            $this->info(__('hr.commands.no_expiring_documents'));
            return self::SUCCESS;
        }

        foreach ($rows as $row) {
            $employee = $row['employee'];
            $documents = collect([
                $row['passport_expiring'] ? __('hr.attachments.passport') : null,
                $row['iqama_expiring'] ? __('hr.attachments.iqama') : null,
            ])->filter()->implode(', ');

            $this->warn(__('hr.commands.expiring_document_line', [
                'employee' => $employee->full_name,
                'documents' => $documents,
            ]));
        }

        return self::SUCCESS;
    }
}
