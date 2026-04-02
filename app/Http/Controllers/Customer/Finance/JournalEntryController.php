<?php

namespace App\Http\Controllers\Customer\Finance;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\Finance\JournalEntryStoreRequest;
use App\Models\Finance\JournalEntry;
use App\Services\Finance\AccountService;
use App\Services\Finance\FinanceContextService;
use App\Services\Finance\JournalEntryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class JournalEntryController extends Controller
{
    public function __construct(
        private readonly JournalEntryService $entries,
        private readonly AccountService $accounts,
        private readonly FinanceContextService $context
    ) {}

    public function index(string $locale): View
    {
        $tenantId = $this->context->tenantIdOrFail(auth()->user());
        $rows = $this->entries->paginate($tenantId, request()->only(['date_from', 'date_to']));

        return view('dashboard.customer.finance.journal_entries.index', compact('rows'));
    }

    public function create(string $locale): View
    {
        $tenantId = $this->context->tenantIdOrFail(auth()->user());
        $accounts = $this->accounts->listByTenant($tenantId);

        return view('dashboard.customer.finance.journal_entries.create', compact('accounts'));
    }

    public function store(JournalEntryStoreRequest $request, string $locale): RedirectResponse
    {
        $tenantId = $this->context->tenantIdOrFail(auth()->user());
        $payload = $request->validated();
        $payload['created_by'] = auth()->id();

        $this->entries->create($tenantId, $payload);

        return redirect()->route('customer.finance.journal-entries.index', ['locale' => $locale])
            ->with('success', __('finance.messages.created', ['entity' => __('finance.entities.journal_entry')]));
    }

    public function show(string $locale, JournalEntry $journalEntry): View
    {
        $this->authorizeTenant($journalEntry->tenant_id);
        $journalEntry->load(['lines.account']);

        return view('dashboard.customer.finance.journal_entries.show', compact('journalEntry'));
    }

    private function authorizeTenant(string $tenantId): void
    {
        if ((string) auth()->user()->tenant_id !== $tenantId) {
            abort(403);
        }
    }
}
