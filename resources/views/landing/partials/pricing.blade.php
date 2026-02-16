<section id="pricing" class="section-padding bg-light py-5">
    <div class="container">
        <!-- Section Heading -->
        <div class="text-center mb-5">
            <h2 class="h1">{!! __('app.pricing_title') !!}</h2>

            <!-- Billing toggle -->
            <div class="btn-group toggle-billing mt-3" role="group" aria-label="Billing Toggle">
                <button type="button" class="btn billing-toggle me-2 " data-cycle="monthly">Monthly</button>
                <button type="button" class="btn billing-toggle me-2" data-cycle="weekly">Weekly</button>
                <button type="button" class="btn billing-toggle me-2" data-cycle="yearly">Yearly</button>
                <button type="button" class="btn billing-toggle me-2 active" data-cycle="all">All</button>
            </div>

        </div>

        <!-- Pricing Cards -->
        <div class="row g-4 justify-content-center">
            @forelse ($plans as $plan)
                <div class="col-md-4">
                    <div class="card pricing-card h-100 shadow-sm border-light-green {{ $plan->is_featured ? 'featured-plan' : '' }}"
                        data-cycle="{{ $plan->billing_cycle }}" data-monthly="{{ $plan->price }}"
                        data-weekly="{{ $plan->price_weekly ?? $plan->price }}"
                        data-yearly="{{ $plan->price_yearly ?? $plan->price }}"
                        data-currency="{{ $plan->currency->symbol ?? '' }}"
                        data-monthly-label="{{ __('app.' . $plan->billing_cycle) }}"
                        data-weekly-label="{{ __('app.' . $plan->billing_cycle) }}"
                        data-yearly-label="{{ __('app.' . $plan->billing_cycle) }}">

                        @if($plan->is_featured)
                            <div class="d-flex  align-items-center justify-content-center">

                                <span
                                    class="badge  bg-success col-4  mt-3 px-3 py-2 featured-badge">
                                    {{ __('app.most_popular') }}
                                </span>
                            </div>
                        @endif

                        <div class="card-body d-flex flex-column text-center">
                            <h5 class="text-muted">{{ $plan->name }}</h5>
                            <h2 class="my-4 price-display">
                                {{ number_format($plan->price, 2) }} {{ $plan->currency->symbol ?? '' }}
                                <small class="text-muted">{{  '/ ' . __('app.' . $plan->billing_cycle)  }}</small>
                            </h2>

                            <ul class="list-unstyled mb-4 text-start">
                                @foreach ($plan->resolved_features as $feature)
                                    @if($feature['enabled'])
                                        <li
                                            class="mb-2 d-flex align-items-center @if(!empty($feature['extra'])) extra-feature @endif">
                                            <i class="bi bi-check2-circle text-success me-2"></i>
                                            {{ $feature['label'] }}
                                            @if(!empty($feature['value']))
                                                : {{ $feature['value'] }}
                                            @endif
                                        </li>
                                    @endif
                                @endforeach
                            </ul>

                            <div class="mt-auto">
                                @if($plan->slug === 'enterprise')
                                    <button
                                        class="btn btn-outline-dark w-100 rounded-pill py-2">{{ __('app.contact_sales') }}</button>
                                @elseif($plan->slug === 'business_pro')
                                    <button
                                        class="btn btn-primary w-100 rounded-pill py-3">{{ __('app.start_free_trial') }}</button>
                                @else
                                    <button
                                        class="btn btn-outline-dark w-100 rounded-pill py-2">{{ __('app.get_started') }}</button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-center text-muted">{{ __('app.no_plans_available') }}</p>
            @endforelse
        </div>
    </div>
</section>