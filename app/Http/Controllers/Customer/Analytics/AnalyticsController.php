<?php

namespace App\Http\Controllers\Customer\Analytics;

use App\Http\Controllers\Controller;
use App\Services\Analytics\AnalyticsService;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
    public function __construct(private readonly AnalyticsService $analyticsService)
    {
    }

    public function index(string $locale): View
    {
        $tenantId = auth()->user()?->tenant_id;
        $report = $this->analyticsService->dashboard($tenantId);

        return view('dashboard.customer.analytics.index', compact('report'));
    }
}
