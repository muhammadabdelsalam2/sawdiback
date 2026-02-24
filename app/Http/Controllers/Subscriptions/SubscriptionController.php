<?php

namespace App\Http\Controllers\Subscriptions;

use App\Http\Controllers\Controller;
use App\Http\Requests\Subscriptions\SubscriptionCancelRequest;
use App\Http\Requests\Subscriptions\SubscriptionChangePlanRequest;
use App\Http\Requests\Subscriptions\SubscriptionExpireRequest;
use App\Http\Requests\Subscriptions\SubscriptionRenewRequest;
use App\Http\Requests\Subscriptions\SubscriptionStoreRequest;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Services\Subscriptions\SubscriptionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Http\Requests\Subscriptions\SubscriptionApproveRequest;
use App\Http\Requests\Subscriptions\SubscriptionRejectRequest;


class SubscriptionController extends Controller
{
    public function __construct(private readonly SubscriptionService $subscriptionService)
    {
    }

    public function index(string $locale): View
    {
        $subscriptions = Subscription::query()
            ->with(['customer', 'plan.currency'])
            ->orderByDesc('id')
            ->paginate(15);

        return view('dashboard.subscriptions.subscriptions.index', compact('subscriptions'));
    }

    public function create(string $locale): View
    {
        $customers = User::query()->orderBy('name')->get();
        $plans = Plan::query()->with('currency')->where('is_active', true)->orderBy('sort_order')->get();

        return view('dashboard.subscriptions.subscriptions.create', compact('customers', 'plans'));
    }

    public function store(SubscriptionStoreRequest $request, string $locale): RedirectResponse
    {
        $this->subscriptionService->create($request->validated(), auth()->id());

        return redirect()
            ->route('superadmin.subscriptions.index', ['locale' => session('locale_full', 'en-SA')])
            ->with('success', __('subscriptions.messages.subscription_created'));
    }

    public function show(string $locale, Subscription $subscription): View
    {
        $subscription->load(['customer', 'plan.currency', 'histories.actor']);
        $plans = Plan::query()->with('currency')->where('is_active', true)->orderBy('sort_order')->get();

        return view('dashboard.subscriptions.subscriptions.show', compact('subscription', 'plans'));
    }

    public function changePlan(SubscriptionChangePlanRequest $request, string $locale, Subscription $subscription): RedirectResponse
    {
        $this->subscriptionService->upgradeOrDowngrade(
            $subscription,
            (int) $request->validated('plan_id'),
            auth()->id()
        );

        return redirect()
            ->route('superadmin.subscriptions.show', ['locale' => session('locale_full', 'en-SA'), 'subscription' => $subscription->id])
            ->with('success', __('subscriptions.messages.subscription_plan_changed'));
    }

    public function renew(SubscriptionRenewRequest $request, string $locale, Subscription $subscription): RedirectResponse
    {
        $this->subscriptionService->renew(
            $subscription,
            $request->validated('from_date'),
            auth()->id()
        );

        return redirect()
            ->route('superadmin.subscriptions.show', ['locale' => session('locale_full', 'en-SA'), 'subscription' => $subscription->id])
            ->with('success', __('subscriptions.messages.subscription_renewed'));
    }

    public function cancel(SubscriptionCancelRequest $request, string $locale, Subscription $subscription): RedirectResponse
    {
        $this->subscriptionService->cancel($subscription, auth()->id());

        return redirect()
            ->route('superadmin.subscriptions.show', ['locale' => session('locale_full', 'en-SA'), 'subscription' => $subscription->id])
            ->with('success', __('subscriptions.messages.subscription_canceled'));
    }

    public function expire(SubscriptionExpireRequest $request, string $locale, Subscription $subscription): RedirectResponse
    {
        $this->subscriptionService->expire($subscription, auth()->id());

        return redirect()
            ->route('superadmin.subscriptions.show', ['locale' => session('locale_full', 'en-SA'), 'subscription' => $subscription->id])
            ->with('success', __('subscriptions.messages.subscription_expired'));
    }

    public function approve(SubscriptionApproveRequest $request, string $locale, Subscription $subscription): RedirectResponse
{
    $this->subscriptionService->approvePending($subscription, auth()->id());

    return redirect()
        ->route('superadmin.subscriptions.show', ['locale' => session('locale_full', 'en-SA'), 'subscription' => $subscription->id])
        ->with('success', __('subscriptions.messages.subscription_approved'));
}

public function reject(SubscriptionRejectRequest $request, string $locale, Subscription $subscription): RedirectResponse
{
    $this->subscriptionService->rejectPending(
        $subscription,
        $request->validated('reason'),
        auth()->id()
    );

    return redirect()
        ->route('superadmin.subscriptions.show', ['locale' => session('locale_full', 'en-SA'), 'subscription' => $subscription->id])
        ->with('success', __('subscriptions.messages.subscription_rejected'));
}

}
