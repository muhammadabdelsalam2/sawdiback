<?php

namespace App\Http\Controllers\Subscriptions;

use App\Http\Controllers\Controller;
use App\Http\Requests\Subscriptions\PlanFeatureSyncRequest;
use App\Http\Requests\Subscriptions\PlanStoreRequest;
use App\Http\Requests\Subscriptions\PlanUpdateRequest;
use App\Models\Currency;
use App\Models\Feature;
use App\Models\Plan;
use App\Repositories\PlanRepository;
use App\Services\PlanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PlanController extends Controller
{
    protected PlanRepository $planRepository;
    protected PlanService $planService;

    public function __construct(PlanRepository $planRepository, PlanService $planService)
    {
        $this->planRepository = $planRepository;
        $this->planService = $planService;
    }
    public function index(string $locale): View
    {
        $plans = $this->planRepository->paginate(15);

        return view('dashboard.subscriptions.plans.index', compact('plans'));
    }

    public function create(string $locale): View
    {
        $currencies = Currency::query()->orderBy('code')->get();

        return view('dashboard.subscriptions.plans.create', compact('currencies'));
    }

    public function store(PlanStoreRequest $request, string $locale): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);
        $data['sort_order'] = $request->integer('sort_order', 0);

        $plan = $this->planService->createPlan($data);

        return redirect()
            ->route('superadmin.plans.index', ['locale' => session('locale_full', 'en-SA')])
            ->with('success', __('subscriptions.messages.plan_created'));
    }

    public function edit(string $locale, Plan $plan): View
    {
        $currencies = Currency::query()->orderBy('code')->get();

        return view('dashboard.subscriptions.plans.edit', compact('plan', 'currencies'));
    }

    public function update(PlanUpdateRequest $request, string $locale, Plan $plan): RedirectResponse
    {
        $plan->update([
            ...$request->validated(),
            'is_active' => $request->boolean('is_active'),
            'sort_order' => $request->integer('sort_order', 0),
        ]);

        return redirect()
            ->route('superadmin.plans.index', ['locale' => session('locale_full', 'en-SA')])
            ->with('success', __('subscriptions.messages.plan_updated'));
    }

    public function destroy(string $locale, Plan $plan): RedirectResponse
    {
        $plan->delete();

        return redirect()
            ->route('superadmin.plans.index', ['locale' => session('locale_full', 'en-SA')])
            ->with('success', __('subscriptions.messages.plan_deleted'));
    }

    public function editFeatures(string $locale, Plan $plan): View
    {
        // جلب الـ plan مع العلاقات المطلوبة
        $plan = $this->planRepository->findWithRelations($plan->id, ['currency']);

        // استخدام الـ Service لحل features ودمج القيم مع التعريفات
        $features = $this->planService->resolvedFeatures($plan->features ?? []);

        // تمرير البيانات للـ view
        return view('dashboard.subscriptions.plans.features', compact('plan', 'features'));
    }

    public function updateFeatures(PlanFeatureSyncRequest $request, string $locale, Plan $plan): RedirectResponse
    {
        try {
            $featuresInput = $request->validated('features', []);
            $this->planService->updateFeatures($plan, $featuresInput);

            return redirect()
                ->route('superadmin.plans.features.edit', ['locale' => session('locale_full', 'en-SA'), 'plan' => $plan->id])
                ->with('success', __('subscriptions.messages.plan_features_updated'));

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()
                ->back()
                ->withErrors($e->errors())
                ->withInput();
        }
    }


}
