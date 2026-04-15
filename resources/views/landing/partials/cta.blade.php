{{-- <section class="section-padding">
    <div class="container">
        <div class="bg-dark p-5 rounded-5 text-center text-white position-relative overflow-hidden"
            style="background-color: #1a1a1a !important;">

            <h2 class="display-5 text-white mb-4">{{ __('app.cta_title') }}</h2>

            <p class="lead opacity-75 mb-5">{{ __('app.cta_text') }}</p>

            <button class="btn btn-primary-farm btn-lg px-5">{{ __('app.cta_button') }}</button>

            <div class="position-absolute bottom-0 start-0 w-100 h-10 bg-green opacity-10"
                style="background: var(--brand-green); height: 5px;"></div>
        </div>
    </div>
</section> --}}
<section class="py-5 bg-cream">
    <div class="container">
        <div class="row align-items-center g-5">

            <div class="col-lg-6">
                <img src="{{ asset('assets/images/elsawady.png') }}"
                     class="img-fluid rounded-5 shadow-lg"
                     alt="Elsawady Farm">
            </div>

            <div class="col-lg-6">
                <h2 class="display-5 fw-bold mb-4">
                    {{ __('app.about_title') }}
                </h2>

                <p class="text-muted fs-5 mb-4">
                    {{ __('app.about_text') }}
                </p>

                <div class="d-flex gap-4">
                    <div>
                        <h3 class="fw-bold text-success">15+</h3>
                        <small class="text-muted">{{ __('app.years_experience') }}</small>
                    </div>
                    <div>
                        <h3 class="fw-bold text-success">100%</h3>
                        <small class="text-muted">{{ __('app.natural_products') }}</small>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
