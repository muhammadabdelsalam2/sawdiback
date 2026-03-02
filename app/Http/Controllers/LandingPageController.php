<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Models\InventoryProduct;
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
       $products = InventoryProduct::where('is_active', true)->get();

        // Pass plans & currency to the view
        return view('landing.index', compact('products', 'currencyId'));
    }

}
