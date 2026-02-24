@extends('layouts.customer.dashboard')

@section('content')
<div class="container py-4">

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <h3 class="mb-3">My Subscription</h3>

    @php
        $isPending = isset($pendingRequest) && $pendingRequest;
        $hasActive = isset($activeSubscription) && $activeSubscription;
        $activePlanId = $hasActive ? (int) $activeSubscription->plan_id : null;
    @endphp

    @if($subscription)
        <div class="card mb-4">
            <div class="card-body">
                <div><strong>Status:</strong> {{ $subscription->status }}</div>
                <div><strong>Plan ID:</strong> {{ $subscription->plan_id }}</div>
                <div><strong>Start:</strong> {{ optional($subscription->start_at)->format('Y-m-d H:i') }}</div>
                <div><strong>Renewal:</strong> {{ optional($subscription->renewal_at)->format('Y-m-d H:i') }}</div>
                <div><strong>End:</strong> {{ optional($subscription->end_at)->format('Y-m-d H:i') }}</div>

                {{-- Pending info --}}
                @if($isPending)
                    <div class="alert alert-warning mt-3 mb-0">
                        Your request is pending. Please wait for SuperAdmin approval/payment processing.
                    </div>
                @endif

                {{-- Cancel only when ACTIVE --}}
                @if($hasActive && $activeSubscription->status === \App\Models\Subscription::STATUS_ACTIVE)
                    <form method="POST" action="{{ route('customer.subscription.cancel', ['locale' => request()->route('locale')]) }}" class="mt-3">
                        @csrf
                        <button class="btn btn-outline-danger" type="submit">Cancel Subscription</button>
                    </form>
                @endif
            </div>
        </div>
    @else
        <div class="alert alert-info mb-4">No subscription yet. Choose a plan below.</div>
    @endif

    <h4 class="mb-3">Available Plans</h4>

    <div class="card">
        <div class="card-body">
            @if($plans->count() === 0)
                <div class="text-muted">No active plans available.</div>
            @else
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Price</th>
                                <th>Billing</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($plans as $plan)
                            @php $planId = (int) $plan->id; @endphp
                            <tr>
                                <td>{{ $plan->name }}</td>
                                <td>{{ $plan->price }} {{ optional($plan->currency)->code ?? '' }}</td>
                                <td>{{ $plan->billing_cycle }}</td>
                                <td class="text-end">

                                    {{-- If there is a pending request, lock all actions --}}
                                    @if($isPending)
                                        <span class="badge bg-warning text-dark">Pending</span>

                                    {{-- If there is an active subscription --}}
                                    @elseif($hasActive)
                                        {{-- Current plan: remove button --}}
                                        @if($activePlanId === $planId)
                                            <span class="badge bg-secondary">Current plan</span>
                                        @else
                                            {{-- Request change (not immediate change) --}}
                                            <form method="POST" action="{{ route('customer.subscription.change-plan', ['locale' => request()->route('locale')]) }}">
                                                @csrf
                                                <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                                                <button class="btn btn-sm btn-primary" type="submit">Request change</button>
                                            </form>
                                        @endif

                                    {{-- No active subscription --}}
                                    @else
                                        <form method="POST" action="{{ route('customer.subscription.subscribe', ['locale' => request()->route('locale')]) }}">
                                            @csrf
                                            <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                                            <button class="btn btn-sm btn-success" type="submit">Request subscription</button>
                                        </form>
                                    @endif

                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $plans->links() }}
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
