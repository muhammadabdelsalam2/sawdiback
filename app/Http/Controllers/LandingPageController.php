<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use Illuminate\Http\Request;
use App\Repositories\PlanRepository;
use App\Services\PlanService;
class LandingPageController extends Controller
{
    protected PlanRepository $planRepository;
    protected PlanService $planService;

    public function __construct(PlanRepository $planRepository, PlanService $planService)
    {
        $this->planRepository = $planRepository;
        $this->planService = $planService;
    }
    // Show the landing page
    public function index(Request $request)
    {
        // Get current locale & currency
        $locale = session('locale_full', 'en-SA');
        $currentCurrency = session('currency'); // Use session or default
        $currency = Currency::where('symbol', $currentCurrency)->first();
        $currencyId = $currency?->id;

        // Get all active plans with currency
        $plans = $this->planRepository->allWithRelations(['currency'])
            ->filter(fn($plan) => $plan->is_active);

        // Resolve features & attach all billing prices
        $plans->transform(function ($plan) {
            $plan->resolved_features = $this->planService->resolvedFeatures($plan->features ?? []);

            // Ensure all billing cycles exist for frontend
            $plan->display_price_monthly = $plan->price;
            $plan->display_price_weekly = $plan->price_weekly ?? $plan->price;
            $plan->display_price_yearly = $plan->price_yearly ?? $plan->price;

            $plan->currency_symbol = $plan->currency->symbol ?? '';

            return $plan;
        });

        // Pass plans & currency to the view
        return view('landing.index', compact('plans', 'currencyId'));
    }

}
