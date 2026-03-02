{{-- <section class="hero-section overflow-hidden">
    <div class="container text-center">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <h1 class="display-3 mb-4">{!! __('app.hero_title') !!}</h1>
                <p class="lead text-muted mb-5 fs-5">{{ __('app.hero_text') }}</p>

                <div class="d-flex justify-content-center gap-3 mb-5">
                    <button class="btn btn-primary-farm btn-lg">{{ __('app.start_trial') }}</button>
                    <button class="btn btn-light btn-lg border px-4">{{ __('app.request_demo') }}</button>
                </div>

                <div class="swiper heroSwiper pb-5">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide px-2">
                            <img src="{{ asset('assets/images/image1.png') }}"
                                class="img-fluid rounded-4 shadow-lg border" alt="Main Dashboard">
                        </div>
                        <div class="swiper-slide px-2">
                            <img src="{{ asset('assets/images/image2.png') }}"
                                class="img-fluid rounded-4 shadow-lg border" alt="Finance Module">
                        </div>
                        <div class="swiper-slide px-2">
                            <img src="{{ asset('assets/images/image3.png') }}"
                                class="img-fluid rounded-4 shadow-lg border" alt="Inventory Module">
                        </div>
                    </div>
                    <div class="swiper-pagination"></div>
                </div>
            </div>
        </div>
    </div>
</section> --}}


<section class="hero-section position-relative overflow-hidden py-5">

    <div class="hero-overlay"></div>

    <div class="container position-relative text-center text-white">
        <div class="row justify-content-center">
            <div class="col-lg-9">

                <span class="badge bg-light text-success mb-3 px-3 py-2 rounded-pill">
                    {{ __('app.organic_badge') }}
                </span>

                <h1 class="display-2 text-white fw-bold mb-4">
                    {{ __('app.hero_title') }}
                </h1>

                <p class="lead mb-5 fs-5 opacity-75">
                    {{ __('app.hero_text') }}
                </p>

                <div class="d-flex justify-content-center gap-3 flex-wrap">
                    <a href="#products" class="btn btn-success btn-lg px-5 rounded-pill shadow">
                        {{ __('app.explore_products') }}
                    </a>

                    <a href="#contact" class="btn btn-outline-light btn-lg px-5 rounded-pill">
                        {{ __('app.contact_us') }}
                    </a>
                </div>

            </div>
        </div>
    </div>

</section>
