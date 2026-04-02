<?php

namespace App\Services\Finance;

use App\Models\Finance\Account;
use App\Repositories\Contracts\Finance\AccountRepositoryInterface;

class AccountLookupService
{
    public function __construct(
        private readonly AccountRepositoryInterface $accounts
    ) {}

    public function cash(string $tenantId): Account
    {
        return $this->requireByCode($tenantId, '1010', 'Cash');
    }

    public function bank(string $tenantId): Account
    {
        return $this->requireByCode($tenantId, '1020', 'Bank');
    }

    public function accountsReceivable(string $tenantId): Account
    {
        return $this->requireByCode($tenantId, '1200', 'Accounts Receivable');
    }

    public function accountsPayable(string $tenantId): Account
    {
        return $this->requireByCode($tenantId, '2100', 'Accounts Payable');
    }

    public function inventory(string $tenantId): Account
    {
        return $this->requireByCode($tenantId, '1300', 'Inventory');
    }

    public function revenue(string $tenantId): Account
    {
        return $this->requireByCode($tenantId, '4100', 'Revenue');
    }

    public function expenses(string $tenantId): Account
    {
        return $this->requireByCode($tenantId, '5100', 'Expenses');
    }

    public function paymentAccountByMethod(string $tenantId, string $method): Account
    {
        return match ($method) {
            'bank' => $this->bank($tenantId),
            'cash' => $this->cash($tenantId),
            default => $this->cash($tenantId),
        };
    }

    private function requireByCode(string $tenantId, string $code, string $fallbackName): Account
    {
        $account = $this->accounts->findByCode($tenantId, $code);

        if (!$account) {
            abort(422, __('finance.messages.missing_account', ['account' => $fallbackName]));
        }

        return $account;
    }
}
