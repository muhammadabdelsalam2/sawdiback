<?php

namespace App\Http\Controllers\Customer\Finance;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\Finance\ExpenseStoreRequest;
use App\Http\Requests\Customer\Finance\ExpenseUpdateRequest;
use App\Models\Farm;
use App\Models\Finance\Expense;
use App\Services\Finance\AccountService;
use App\Services\Finance\ExpenseService;
use App\Services\Finance\FinanceContextService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    public function __construct(
        private readonly ExpenseService $expenses,
        private readonly AccountService $accounts,
        private readonly FinanceContextService $context
    ) {}

    public function index(string $locale): View
    {
        $tenantId = $this->context->tenantIdOrFail(auth()->user());
        $rows = $this->expenses->paginate($tenantId, request()->only(['date_from', 'date_to', 'status']));

        return view('dashboard.customer.finance.expenses.index', compact('rows'));
    }

    public function create(string $locale): View
    {
        $tenantId = $this->context->tenantIdOrFail(auth()->user());
        $expenseAccounts = $this->accounts->listByTypes($tenantId, ['expense']);
        $paymentAccounts = $this->accounts->listByTypes($tenantId, ['asset']);
        $farms = Farm::query()->where('tenant_id', $tenantId)->orderBy('name')->get();

        return view('dashboard.customer.finance.expenses.create', compact('expenseAccounts', 'paymentAccounts', 'farms'));
    }

    public function store(ExpenseStoreRequest $request, string $locale): RedirectResponse
    {
        $tenantId = $this->context->tenantIdOrFail(auth()->user());
        $payload = $request->validated();
        $payload['created_by'] = auth()->id();

        $this->expenses->create($tenantId, $payload);

        return redirect()->route('customer.finance.expenses.index', ['locale' => $locale])
            ->with('success', __('finance.messages.created', ['entity' => __('finance.entities.expense')]));
    }

    public function edit(string $locale, Expense $expense): View
    {
        $this->authorizeTenant($expense->tenant_id);
        $tenantId = $this->context->tenantIdOrFail(auth()->user());
        $expenseAccounts = $this->accounts->listByTypes($tenantId, ['expense']);
        $paymentAccounts = $this->accounts->listByTypes($tenantId, ['asset']);
        $farms = Farm::query()->where('tenant_id', $tenantId)->orderBy('name')->get();

        return view('dashboard.customer.finance.expenses.edit', compact('expense', 'expenseAccounts', 'paymentAccounts', 'farms'));
    }

    public function update(ExpenseUpdateRequest $request, string $locale, Expense $expense): RedirectResponse
    {
        $this->authorizeTenant($expense->tenant_id);
        $payload = $request->validated();

        $this->expenses->update($expense, $payload);

        return redirect()->route('customer.finance.expenses.index', ['locale' => $locale])
            ->with('success', __('finance.messages.updated', ['entity' => __('finance.entities.expense')]));
    }

    public function destroy(string $locale, Expense $expense): RedirectResponse
    {
        $this->authorizeTenant($expense->tenant_id);
        $this->expenses->delete($expense);

        return redirect()->route('customer.finance.expenses.index', ['locale' => $locale])
            ->with('success', __('finance.messages.deleted', ['entity' => __('finance.entities.expense')]));
    }

    private function authorizeTenant(string $tenantId): void
    {
        if ((string) auth()->user()->tenant_id !== $tenantId) {
            abort(403);
        }
    }
}
