<section id="pricing" class="section-padding">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="h1">{!! __('app.pricing_title') !!}</h2>
        </div>
        <div class="row g-4 align-items-center justify-content-center">

            <!-- Starter Plan -->
            <div class="col-md-4">
                <div class="pricing-card p-5">
                    <h5 class="text-muted">{{ __('app.starter') }}</h5>
                    <h2 class="my-4">{!! __('app.starter_price') !!}</h2>
                    <ul class="list-unstyled mb-5">
                        <li class="mb-3">{{ __('app.starter_feature_1') }}</li>
                        <li class="mb-3">{{ __('app.starter_feature_2') }}</li>
                        <li class="mb-3">{{ __('app.starter_feature_3') }}</li>
                    </ul>
                    <button class="btn btn-outline-dark w-100 rounded-pill py-2">{{ __('app.get_started') }}</button>
                </div>
            </div>

            <!-- Business Pro Plan -->
            <div class="col-md-4">
                <div class="pricing-card featured p-5">
                    <div class="badge bg-success mb-3" style="background-color: var(--brand-green) !important;">
                        {{ __('app.most_popular') }}
                    </div>
                    <h5 class="text-muted">{{ __('app.business_pro') }}</h5>
                    <h2 class="my-4">{!! __('app.business_pro_price') !!}</h2>
                    <ul class="list-unstyled mb-5 text-start">
                        <li class="mb-3">{{ __('app.business_feature_1') }}</li>
                        <li class="mb-3">{{ __('app.business_feature_2') }}</li>
                        <li class="mb-3">{{ __('app.business_feature_3') }}</li>
                        <li class="mb-3">{{ __('app.business_feature_4') }}</li>
                    </ul>
                    <button class="btn btn-primary-farm w-100 py-3">{{ __('app.start_free_trial') }}</button>
                </div>
            </div>

            <!-- Enterprise Plan -->
            <div class="col-md-4">
                <div class="pricing-card p-5">
                    <h5 class="text-muted">{{ __('app.enterprise') }}</h5>
                    <h2 class="my-4">{!! __('app.enterprise_price') !!}</h2>
                    <ul class="list-unstyled mb-5">
                        <li class="mb-3">{{ __('app.enterprise_feature_1') }}</li>
                        <li class="mb-3">{{ __('app.enterprise_feature_2') }}</li>
                        <li class="mb-3">{{ __('app.enterprise_feature_3') }}</li>
                    </ul>
                    <button class="btn btn-outline-dark w-100 rounded-pill py-2">{{ __('app.contact_sales') }}</button>
                </div>
            </div>

        </div>
    </div>
</section>