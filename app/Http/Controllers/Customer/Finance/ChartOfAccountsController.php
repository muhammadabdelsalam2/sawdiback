<?php

namespace App\Http\Controllers\Customer\Finance;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\Finance\AccountStoreRequest;
use App\Http\Requests\Customer\Finance\AccountUpdateRequest;
use App\Models\Finance\Account;
use App\Services\Finance\AccountService;
use App\Services\Finance\FinanceContextService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ChartOfAccountsController extends Controller
{
    public function __construct(
        private readonly AccountService $accounts,
        private readonly FinanceContextService $context
    ) {}

    public function index(string $locale): View
    {
        $tenantId = $this->context->tenantIdOrFail(auth()->user());
        $rows = $this->accounts->listByTenant($tenantId);

        return view('dashboard.customer.finance.accounts.index', compact('rows'));
    }

    public function create(string $locale): View
    {
        $tenantId = $this->context->tenantIdOrFail(auth()->user());
        $accounts = $this->accounts->listByTenant($tenantId);

        return view('dashboard.customer.finance.accounts.create', compact('accounts'));
    }

    public function store(AccountStoreRequest $request, string $locale): RedirectResponse
    {
        $tenantId = $this->context->tenantIdOrFail(auth()->user());
        $this->accounts->create($tenantId, $request->validated());

        return redirect()->route('customer.finance.accounts.index', ['locale' => $locale])
            ->with('success', __('finance.messages.created', ['entity' => __('finance.entities.account')]));
    }

    public function edit(string $locale, Account $account): View
    {
        $this->authorizeTenant($account->tenant_id);
        $tenantId = $this->context->tenantIdOrFail(auth()->user());
        $accounts = $this->accounts->listByTenant($tenantId)->where('id', '!=', $account->id);

        return view('dashboard.customer.finance.accounts.edit', compact('account', 'accounts'));
    }

    public function update(AccountUpdateRequest $request, string $locale, Account $account): RedirectResponse
    {
        $this->authorizeTenant($account->tenant_id);
        $this->accounts->update($account, $request->validated());

        return redirect()->route('customer.finance.accounts.index', ['locale' => $locale])
            ->with('success', __('finance.messages.updated', ['entity' => __('finance.entities.account')]));
    }

    public function destroy(string $locale, Account $account): RedirectResponse
    {
        $this->authorizeTenant($account->tenant_id);
        $this->accounts->delete($account);

        return redirect()->route('customer.finance.accounts.index', ['locale' => $locale])
            ->with('success', __('finance.messages.deleted', ['entity' => __('finance.entities.account')]));
    }

    private function authorizeTenant(string $tenantId): void
    {
        if ((string) auth()->user()->tenant_id !== $tenantId) {
            abort(403);
        }
    }
}
