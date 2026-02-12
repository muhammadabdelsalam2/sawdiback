<?php

namespace App\Http\Controllers\Subscriptions;

use App\Http\Controllers\Controller;
use App\Http\Requests\Subscriptions\PlanFeatureSyncRequest;
use App\Http\Requests\Subscriptions\PlanStoreRequest;
use App\Http\Requests\Subscriptions\PlanUpdateRequest;
use App\Models\Currency;
use App\Models\Feature;
use App\Models\Plan;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PlanController extends Controller
{
    public function index(string $locale): View
    {
        $plans = Plan::query()
            ->with(['currency', 'features'])
            ->orderBy('sort_order')
            ->orderBy('id', 'desc')
            ->paginate(15);

        return view('dashboard.subscriptions.plans.index', compact('plans'));
    }

    public function create(string $locale): View
    {
        $currencies = Currency::query()->orderBy('code')->get();

        return view('dashboard.subscriptions.plans.create', compact('currencies'));
    }

    public function store(PlanStoreRequest $request, string $locale): RedirectResponse
    {
        Plan::query()->create([
            ...$request->validated(),
            'is_active' => $request->boolean('is_active', true),
            'sort_order' => $request->integer('sort_order', 0),
        ]);

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
        $plan->load(['features', 'currency']);
        $features = Feature::query()->where('is_active', true)->orderBy('name')->get();

        return view('dashboard.subscriptions.plans.features', compact('plan', 'features'));
    }

    public function updateFeatures(PlanFeatureSyncRequest $request, string $locale, Plan $plan): RedirectResponse
    {
        $inputFeatures = $request->validated('features', []);

        $syncData = [];
        foreach ($inputFeatures as $featureId => $payload) {
            $syncData[(int) $featureId] = [
                'enabled' => (bool) ($payload['enabled'] ?? false),
                'value' => $payload['value'] ?? null,
            ];
        }

        $plan->features()->sync($syncData);

        return redirect()
            ->route('superadmin.plans.features.edit', ['locale' => session('locale_full', 'en-SA'), 'plan' => $plan->id])
            ->with('success', __('subscriptions.messages.plan_features_updated'));
    }
}
