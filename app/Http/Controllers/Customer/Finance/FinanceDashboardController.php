<?php

namespace App\Http\Controllers\Customer\Finance;

use App\Http\Controllers\Controller;
use App\Services\Finance\FinanceContextService;
use App\Services\Finance\FinanceDashboardService;
use Illuminate\View\View;

class FinanceDashboardController extends Controller
{
    public function __construct(
        private readonly FinanceDashboardService $dashboard,
        private readonly FinanceContextService $context
    ) {}

    public function index(string $locale): View
    {
        $tenantId = $this->context->tenantIdOrFail(auth()->user());
        $summary = $this->dashboard->summary($tenantId, request()->only(['date_from', 'date_to']));

        return view('dashboard.customer.finance.dashboard', compact('summary'));
    }
}
