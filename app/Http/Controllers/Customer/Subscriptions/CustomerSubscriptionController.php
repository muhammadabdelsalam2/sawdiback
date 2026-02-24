<?php

namespace App\Http\Controllers\Customer\Subscriptions;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\Subscriptions\CancelRequest;
use App\Http\Requests\Customer\Subscriptions\ChangePlanRequest;
use App\Http\Requests\Customer\Subscriptions\SubscribeRequest;
use App\Repositories\Contracts\CustomerSubscriptionRepositoryInterface;
use App\Services\Customer\CustomerSubscriptionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CustomerSubscriptionController extends Controller
{
    public function __construct(
        private readonly CustomerSubscriptionService $service,
        private readonly CustomerSubscriptionRepositoryInterface $repo
    ) {}

    public function index(string $locale): View
    {
        $user = auth()->user();
        $tenantId = (string) $user->tenant_id;

        $activeSubscription = $tenantId ? $this->service->getActive($tenantId) : null;
        $pendingRequest     = $tenantId ? $this->service->getPending($tenantId) : null;
        $latestSubscription = $tenantId ? $this->service->getLatest($tenantId) : null;

        // What to show in "My Subscription" card:
        // - If pending exists, show it (because user just requested and is waiting)
        // - Else show active
        // - Else show latest (e.g. expired)
        $subscription = $pendingRequest ?? $activeSubscription ?? $latestSubscription;

        $plans = $this->repo->listActivePlans(15);

        return view('dashboard.customer.subscription.index', [
            'subscription'       => $subscription,
            'activeSubscription' => $activeSubscription,
            'pendingRequest'     => $pendingRequest,
            'plans'              => $plans,
        ]);
    }

    public function subscribe(SubscribeRequest $request, string $locale): RedirectResponse
    {
        $user = auth()->user();

        if (!$user->tenant_id) {
            return back()->withErrors(['tenant' => 'No tenant linked to this user yet.']);
        }

        $this->service->subscribe(
            (string) $user->tenant_id,
            (int) $user->id,
            (int) $request->validated()['plan_id']
        );

        return redirect()
            ->route('customer.subscription.index', ['locale' => $locale])
            ->with('success', 'Subscription request submitted. Waiting for approval/payment.');
    }

    public function changePlan(ChangePlanRequest $request, string $locale): RedirectResponse
    {
        $user = auth()->user();

        if (!$user->tenant_id) {
            return back()->withErrors(['tenant' => 'No tenant linked to this user yet.']);
        }

        $this->service->changePlan(
            (string) $user->tenant_id,
            (int) $user->id,
            (int) $request->validated()['plan_id']
        );

        return redirect()
            ->route('customer.subscription.index', ['locale' => $locale])
            ->with('success', 'Plan change request submitted. Waiting for approval/payment.');
    }

    public function cancel(CancelRequest $request, string $locale): RedirectResponse
    {
        $user = auth()->user();

        if (!$user->tenant_id) {
            return back()->withErrors(['tenant' => 'No tenant linked to this user yet.']);
        }

        $this->service->cancel((string) $user->tenant_id, (int) $user->id);

        return redirect()
            ->route('customer.subscription.index', ['locale' => $locale])
            ->with('success', 'Subscription canceled successfully.');
    }
}
