<footer class="py-5 border-top">
    <div class="container">
        <div class="row justify-content-between">
            <div class="col-md-4 mb-4">
                <h5 class="fw-bold mb-3" style="color: var(--farm-green);">{{ __('app.footer_brand') }}</h5>
                <p class="text-muted">{{ __('app.footer_text') }}</p>
            </div>
            <div class="col-md-2 mb-4">
                <h6 class="fw-bold">{{ __('app.footer_platform') }}</h6>
                <ul class="list-unstyled text-muted">
                    <li>{{ __('app.footer_dashboard') }}</li>
                    <li>{{ __('app.footer_livestock') }}</li>
                    <li>{{ __('app.footer_finance') }}</li>
                </ul>
            </div>
            <div class="col-md-3">
                <h6 class="fw-bold">{{ __('app.footer_newsletter') }}</h6>
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="{{ __('app.footer_email_placeholder') }}">
                    <button class="btn btn-farm">{{ __('app.footer_join_button') }}</button>
                </div>
            </div>
        </div>
    </div>
</footer>