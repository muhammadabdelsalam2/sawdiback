<?php

namespace App\Http\Controllers\Customer\Finance;

use App\Http\Controllers\Controller;
use App\Models\Finance\Account;
use App\Services\Finance\AccountService;
use App\Services\Finance\FinanceContextService;
use App\Services\Finance\LedgerService;
use Illuminate\View\View;

class GeneralLedgerController extends Controller
{
    public function __construct(
        private readonly LedgerService $ledger,
        private readonly AccountService $accounts,
        private readonly FinanceContextService $context
    ) {}

    public function index(string $locale): View
    {
        $tenantId = $this->context->tenantIdOrFail(auth()->user());
        $accounts = $this->accounts->listByTenant($tenantId);

        $accountId = (int) request('account_id');
        $selected = $accountId ? $accounts->firstWhere('id', $accountId) : null;

        $ledgerData = null;
        if ($selected) {
            $ledgerData = $this->ledger->ledger($selected, request()->only(['date_from', 'date_to']));
        }

        return view('dashboard.customer.finance.ledger.index', compact('accounts', 'selected', 'ledgerData'));
    }
}
