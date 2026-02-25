<?php

namespace App\Http\Controllers\Customer\SalesDistribution;

use App\Http\Controllers\Controller;
use App\Services\SalesDistribution\SalesDistributionContextService;
use App\Services\SalesDistribution\SalesDistributionDashboardService;
use Illuminate\View\View;

class SalesDistributionDashboardController extends Controller
{
    public function __construct(
        private readonly SalesDistributionDashboardService $dashboardService,
        private readonly SalesDistributionContextService $context
    ) {}

    public function index(string $locale): View
    {
        $tenantId = $this->context->tenantIdOrFail(auth()->user());
        $summary = $this->dashboardService->summary($tenantId);

        return view('dashboard.customer.sales_distribution.dashboard', compact('summary'));
    }
}
