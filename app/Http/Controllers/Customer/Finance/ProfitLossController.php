<?php

namespace App\Http\Controllers\Customer\Finance;

use App\Http\Controllers\Controller;
use App\Services\Finance\FinanceContextService;
use App\Services\Finance\ProfitLossService;
use Illuminate\View\View;

class ProfitLossController extends Controller
{
    public function __construct(
        private readonly ProfitLossService $reports,
        private readonly FinanceContextService $context
    ) {}

    public function index(string $locale): View
    {
        $tenantId = $this->context->tenantIdOrFail(auth()->user());
        $report = $this->reports->report($tenantId, request()->only(['date_from', 'date_to']));

        return view('dashboard.customer.finance.profit_loss.index', compact('report'));
    }
}
