<section class="hero-section">
    <div class="container text-center">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <h1 class="display-3 mb-4">{!! __('app.hero_title') !!}</h1>
                <p class="lead text-muted mb-5 fs-5">{{ __('app.hero_text') }}</p>
                <div class="d-flex justify-content-center gap-3">
                    <button class="btn btn-primary-farm btn-lg">{{ __('app.start_trial') }}</button>
                    <button class="btn btn-light btn-lg border px-4">{{ __('app.request_demo') }}</button>
                </div>
                <div class="mt-5 pt-4 d-flex justify-content-center">
                    <img src="{{ asset('assets/images/logo.png') }}" class="img-fluid w-50  rounded-4 shadow-lg border"
                        alt="EL-Sawady ERP Dashboard">
                </div>
            </div>
        </div>
    </div>
</section>