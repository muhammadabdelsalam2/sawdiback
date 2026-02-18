<?php

namespace App\Http\Controllers\Customer\Subscriptions;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\Subscriptions\CancelRequest;
use App\Http\Requests\Customer\Subscriptions\ChangePlanRequest;
use App\Http\Requests\Customer\Subscriptions\SubscribeRequest;
use App\Services\Customer\CustomerSubscriptionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CustomerSubscriptionController extends Controller
{
    public function __construct(
        private readonly CustomerSubscriptionService $service
    ) {}

    public function index(string $locale): View
    {
        $user = auth()->user();
        $tenantId = (string) $user->tenant_id;

        $subscription = $tenantId ? $this->service->getCurrent($tenantId) : null;

        // Plans for choosing/upgrading (active only)
        $plans = app(\App\Repositories\Contracts\CustomerSubscriptionRepositoryInterface::class)
            ->listActivePlans(15);

        return view('dashboard.customer.subscription.index', compact('subscription', 'plans'));
    }

    public function subscribe(SubscribeRequest $request, string $locale): RedirectResponse
    {
        $user = auth()->user();

        if (!$user->tenant_id) {
            return back()->withErrors(['tenant' => 'No tenant linked to this user yet.']);
        }

        $this->service->subscribe((string)$user->tenant_id, (int)$user->id, (int)$request->validated()['plan_id']);

        return redirect()
            ->route('customer.subscription.index', ['locale' => $locale])
            ->with('success', 'Subscription activated successfully.');
    }

    public function changePlan(ChangePlanRequest $request, string $locale): RedirectResponse
    {
        $user = auth()->user();

        if (!$user->tenant_id) {
            return back()->withErrors(['tenant' => 'No tenant linked to this user yet.']);
        }

        $this->service->changePlan((string)$user->tenant_id, (int)$user->id, (int)$request->validated()['plan_id']);

        return redirect()
            ->route('customer.subscription.index', ['locale' => $locale])
            ->with('success', 'Plan changed successfully.');
    }

    public function cancel(CancelRequest $request, string $locale): RedirectResponse
    {
        $user = auth()->user();

        if (!$user->tenant_id) {
            return back()->withErrors(['tenant' => 'No tenant linked to this user yet.']);
        }

        $this->service->cancel((string)$user->tenant_id, (int)$user->id);

        return redirect()
            ->route('customer.subscription.index', ['locale' => $locale])
            ->with('success', 'Subscription canceled successfully.');
    }
}
