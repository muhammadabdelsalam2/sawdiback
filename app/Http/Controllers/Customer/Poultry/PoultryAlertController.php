<?php

namespace App\Http\Controllers\Customer\Poultry;

use App\Http\Controllers\Controller;
use App\Services\Poultry\PoultryAlertService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PoultryAlertController extends Controller
{
    public function __invoke(Request $request, PoultryAlertService $alerts): View
    {
        $currentLocale = $request->route('locale') ?? app()->getLocale();
        $tenantId = (string) auth()->user()->tenant_id;

        // استدعاء تنبيهات النفوق والذبح مع مراعاة المستأجر
        $highMortalityCycles = method_exists($alerts, 'highBroilerMortality')
            ? $alerts->highBroilerMortality($tenantId)
            : collect();

        $slaughterDueCycles = method_exists($alerts, 'broilerCyclesNearSlaughter')
            ? $alerts->broilerCyclesNearSlaughter($tenantId)
            : collect();

        return view('dashboard.customer.poultry.alerts.index', [
            'highMortalityCycles' => $highMortalityCycles,
            'slaughterDueCycles'  => $slaughterDueCycles,
            'currentLocale'       => $currentLocale,
        ]);
    }
}