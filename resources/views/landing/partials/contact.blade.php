<section id="contact" class="py-5 bg-white">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6">
                <h2 class="display-5 fw-bold mb-3">{{ __('app.contact_section_title') }}</h2>
                <p class="text-muted fs-5">{{ $contactInfo?->localized('description') ?? __('app.contact_section_text') }}</p>
            </div>

            <div class="col-lg-6">
                <div class="p-4 rounded-4 shadow-sm border">
                    <div class="mb-3">
                        <div class="fw-bold">{{ __('app.contact_phone') }}</div>
                        <div class="text-muted">{{ $contactInfo?->phone ?? '-' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="fw-bold">{{ __('app.contact_email') }}</div>
                        <div class="text-muted">{{ $contactInfo?->email ?? '-' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="fw-bold">{{ __('app.contact_address') }}</div>
                        <div class="text-muted">{{ $contactInfo?->localized('address') ?? '-' }}</div>
                    </div>
                    <div class="mb-4">
                        <div class="fw-bold">{{ __('app.contact_working_hours') }}</div>
                        <div class="text-muted">{{ $contactInfo?->localized('working_hours') ?? '-' }}</div>
                    </div>

                    <div class="d-flex gap-2 flex-wrap">
                        @if ($contactInfo?->whatsapp_url)
                            <a class="btn btn-success rounded-pill" href="{{ $contactInfo->whatsapp_url }}" target="_blank" rel="noopener">
                                <i class="bi bi-whatsapp me-1"></i> {{ __('app.contact_whatsapp') }}
                            </a>
                        @endif
                        @if ($contactInfo?->facebook_url)
                            <a class="btn btn-outline-secondary rounded-pill" href="{{ $contactInfo->facebook_url }}" target="_blank" rel="noopener">Facebook</a>
                        @endif
                        @if ($contactInfo?->instagram_url)
                            <a class="btn btn-outline-secondary rounded-pill" href="{{ $contactInfo->instagram_url }}" target="_blank" rel="noopener">Instagram</a>
                        @endif
                        @if ($contactInfo?->x_url)
                            <a class="btn btn-outline-secondary rounded-pill" href="{{ $contactInfo->x_url }}" target="_blank" rel="noopener">X</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
