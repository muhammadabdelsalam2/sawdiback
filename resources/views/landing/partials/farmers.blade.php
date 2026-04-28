{{-- Sponsored Farmers / ERP Partners --}}
<section class="container py-5">

    {{-- Header --}}
    <div class="text-center mb-4">
        <h2 class="fw-bold text-dark">
            {{ __('farmer.title') }}
        </h2>

        <p class="text-muted small mx-auto" style="max-width: 600px;">
            {{ __('farmer.description') }}
        </p>

        <div class="d-flex flex-wrap justify-content-center gap-2 mt-3">
            <span class="badge bg-success-subtle text-success px-3 py-2">
                {{ __('farmer.verified_partners') }}
            </span>

            <span class="badge bg-primary-subtle text-primary px-3 py-2">
                {{ __('farmer.erp_integrated') }}
            </span>

            <span class="badge bg-secondary-subtle text-dark px-3 py-2">
                {{ __('farmer.sponsored_listings') }}
            </span>
        </div>
    </div>

    {{-- Swiper --}}
    <div class="swiper farmerSwiper">
        <div class="swiper-wrapper">

            @foreach ($farmers as $farmer)
                <div class="swiper-slide h-auto">

                    <div class="card h-100 border-0 shadow-sm">

                        {{-- Top bar --}}
                        <div style="height:4px;background:linear-gradient(90deg,#6366f1,#3b82f6,#06b6d4);"></div>

                        <div class="card-body p-3 d-flex flex-column">

                            {{-- Header --}}
                            <div class="d-flex align-items-center gap-2">

                                {{-- Image --}}
                                <div class="rounded overflow-hidden border bg-light d-flex align-items-center justify-content-center"
                                    style="width:42px;height:42px;flex-shrink:0;">

                                    @if ($farmer->image)
                                        <img src="{{ asset('storage/' . $farmer->image) }}" class="w-100 h-100"
                                            style="object-fit: cover;">
                                    @else
                                        <span class="fw-bold text-success">
                                            {{ strtoupper(substr($farmer->name, 0, 1)) }}
                                        </span>
                                    @endif
                                </div>

                                {{-- Info --}}
                                <div class="flex-grow-1 overflow-hidden">
                                    <div class="fw-bold text-truncate" style="font-size: 14px;">
                                        {{ $farmer->name }}
                                    </div>

                                    <div class="text-muted small text-truncate">
                                        {{ $farmer->farm_name ?? __('farmer.independent_partner') }}
                                    </div>
                                </div>

                                {{-- Status Badge --}}
                                @if ($farmer->is_active)
                                    <span class="badge bg-success-subtle text-success small">
                                        {{ __('farmer.verified') }}
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger small">
                                        {{ __('farmer.inactive') }}
                                    </span>
                                @endif

                            </div>

                            {{-- Meta --}}
                            <div class="row g-2 mt-3 small text-muted">

                                <div class="col-6">
                                    <div class="bg-light p-2 rounded">

                                        <div class="text-muted" style="font-size: 10px;">
                                            {{ __('farmer.location') }}
                                        </div>

                                        <div class="text-truncate">
                                            {{ $farmer->address ?? 'N/A' }}
                                        </div>

                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="bg-light p-2 rounded">

                                        <div class="text-muted" style="font-size: 10px;">
                                            {{ __('farmer.status') }}
                                        </div>

                                        <div>
                                            {{ $farmer->is_active ? __('farmer.active') : __('farmer.suspended') }}
                                        </div>

                                    </div>
                                </div>

                            </div>

                            {{-- Stats --}}
                            <div class="d-flex justify-content-between border-top mt-3 pt-2 small text-muted">

                                <div class="text-center">
                                    <div class="fw-bold text-dark">{{ __('farmer.score') }}</div>
                                    <div>92</div>
                                </div>

                                <div class="text-center">
                                    <div class="fw-bold text-dark">{{ __('farmer.orders') }}</div>
                                    <div>128</div>
                                </div>

                                <div class="text-center">
                                    <div class="fw-bold text-dark">{{ __('farmer.rating') }}</div>
                                    <div>4.8 ★</div>
                                </div>

                            </div>

                            {{-- Button --}}
                            {{-- <a href="{{ route('customer.farmers.show', ['farmer' => $farmer->id, 'locale' => $currentLocale]) }}"
                                class="btn btn-dark btn-sm w-100 mt-3">

                                {{ __('farmer.view_profile') }}

                            </a> --}}

                        </div>
                    </div>

                </div>
            @endforeach

        </div>
    </div>

</section>

{{-- Swiper JS --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        new Swiper(".farmerSwiper", {
            slidesPerView: 1,
            spaceBetween: 16,
            loop: true,
            speed: 600,

            autoplay: {
                delay: 2500,
                disableOnInteraction: false,
            },

            breakpoints: {
                576: {
                    slidesPerView: 1.2
                },
                768: {
                    slidesPerView: 2
                },
                992: {
                    slidesPerView: 3
                }
            }
        });
    });
</script>
