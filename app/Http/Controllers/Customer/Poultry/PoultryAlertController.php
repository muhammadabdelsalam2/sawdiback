<?php

namespace App\Http\Controllers\Customer\Poultry;

use App\Http\Controllers\Controller;
use App\Services\Poultry\PoultryAlertService;
use Illuminate\View\View;

class PoultryAlertController extends Controller
{
    public function __invoke(PoultryAlertService $alerts): View
    {
        return view('dashboard.customer.poultry.alerts.index', [
            'highMortalityCycles' => $alerts->highBroilerMortality(),
            'slaughterDueCycles' => $alerts->broilerCyclesNearSlaughter(),
        ]);
    }
}
