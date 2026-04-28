{{-- Farmers Section --}}
<style>
    document.addEventListener('DOMContentLoaded', function () {
            new Swiper(".farmerSwiper", {
                slidesPerView: 1,
                spaceBetween: 20,
                loop: true,

                pagination: {
                    el: ".swiper-pagination",
                    clickable: true,
                }

                ,

                navigation: {
                    nextEl: ".swiper-button-next",
                    prevEl: ".swiper-button-prev",
                }

                ,

                breakpoints: {
                    640: {
                        slidesPerView: 1,
                    }

                    ,
                    768: {
                        slidesPerView: 2,
                    }

                    ,
                    1024: {
                        slidesPerView: 3,
                    }
                }
            });
    });
</style>
<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">

        {{-- Header --}}
        <div class="text-center mb-12">
            <h2 class="text-4xl font-bold text-gray-800">Our Farmers</h2>
            <p class="text-gray-500 mt-2">Trusted suppliers powering your ERP ecosystem</p>
        </div>

        {{-- Swiper --}}
        <div class="swiper farmerSwiper">
            <div class="swiper-wrapper">

                @foreach($farmers as $farmer)
                    <div class="swiper-slide">

                        <div
                            class="bg-white rounded-2xl shadow-md hover:shadow-xl transition overflow-hidden border border-gray-100">

                            {{-- Top Banner --}}
                            <div class="h-20 bg-gradient-to-r from-green-600 to-green-800"></div>

                            {{-- Avatar --}}
                            <div class="flex justify-center -mt-10">
                                <img src="{{ $farmer->profile_picture_url ?? asset('assets/images/default-farmer.png') }}"
                                    class="w-20 h-20 rounded-full border-4 border-white shadow object-cover"
                                    alt="{{ $farmer->name }}">
                            </div>

                            {{-- Content --}}
                            <div class="p-6 text-center">

                                <h3 class="text-xl font-semibold text-gray-800">
                                    {{ $farmer->name }}
                                </h3>

                                <p class="text-sm text-gray-500 mt-1">
                                    {{ $farmer->farm_name ?? 'Independent Farmer' }}
                                </p>

                                <p class="text-sm text-gray-400 mt-2">
                                    📍 {{ $farmer->address ?? 'No location set' }}
                                </p>

                                {{-- Status --}}
                                <div class="mt-3">
                                    @if($farmer->is_active)
                                        <span class="px-3 py-1 text-xs rounded-full bg-green-100 text-green-700">
                                            Active
                                        </span>
                                    @else
                                        <span class="px-3 py-1 text-xs rounded-full bg-red-100 text-red-600">
                                            Inactive
                                        </span>
                                    @endif
                                </div>

                                {{-- Button --}}
                                <a href="{{ route('customer.farmers.show', ['farmer' => $farmer->id, 'locale' => $currentLocale]) }}"
                                    class="inline-block mt-6 px-5 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                                    View Profile
                                </a>

                            </div>
                        </div>

                    </div>
                @endforeach

            </div>

            {{-- Navigation --}}
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>

            {{-- Pagination --}}
            <div class="swiper-pagination mt-6"></div>
        </div>

    </div>
</section>