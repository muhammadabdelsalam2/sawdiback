<section id="best-selling" class="py-5 bg-white">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold text-success">
                <i class="bi bi-star-fill text-warning me-2"></i>
                {{ __('app.best_selling_title') ?? 'الأكثر مبيعًا' }}
            </h2>
            <p class="text-muted">
                {{ __('app.best_selling_subtitle') ?? 'اكتشف منتجاتنا الأكثر طلباً وتفضيلاً لدى عملائنا' }}
            </p>
        </div>

        <div class="row g-4">
            @forelse($bestSellingProducts as $product)
                <div class="col-md-4">
                    <div class="product-card rounded-5 overflow-hidden shadow-sm bg-light h-100 border-success border-1 position-relative">
                        <div class="position-absolute top-0 end-0 m-3">
                            <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">
                                <i class="bi bi-fire me-1"></i> {{ __('app.hot_deal') ?? 'الأكثر مبيعاً' }}
                            </span>
                        </div>
                        <img src="{{ $product->image_url }}" class="img-fluid w-100" style="height: 250px; object-fit: cover;" alt="{{ $product->name }}">
                        <div class="p-4 text-center">
                            <h5 class="fw-bold">{{ $product->name }}</h5>
                            <p class="text-muted small">{{ $product->notes }}</p>
                            @if($product->tax > 0)
                                <div class="badge bg-soft-success text-success mb-2">
                                    {{ __('warehouse.fields.tax') }}: {{ number_format($product->tax, 2) }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <p class="text-muted">{{ __('app.no_best_selling_products') ?? 'لا توجد منتجات مميزة متاحة حالياً' }}</p>
                </div>
            @endforelse
        </div>
    </div>
</section>
