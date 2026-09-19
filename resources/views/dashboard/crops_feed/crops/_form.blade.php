@csrf
@php
    $currentLocale = request()->route('locale') ?? app()->getLocale();
    $isArabic = str_starts_with(strtolower($currentLocale), 'ar');
    $currentUnit = old('yield_unit', $crop->yield_unit ?? 'ton');
@endphp

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label font-weight-bold">{{ __('farms.fields.farm') }}</label>
        <select name="farm_id" class="form-select">
            <option value="">{{ __('farms.empty.no_farms') }}</option>
            @foreach ($farms ?? [] as $farm)
                <option value="{{ $farm->id }}" @selected(old('farm_id', $crop->farm_id ?? '') == $farm->id)>{{ $farm->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6">
        <label class="form-label font-weight-bold">{{ __('crops_feed.fields.name') }} <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $crop->name ?? '') }}" required>
    </div>

    <div class="col-md-4">
        <label class="form-label font-weight-bold">{{ __('crops_feed.fields.land_area') }} <span class="text-danger">*</span></label>
        <input type="number" step="0.01" min="0.01" name="land_area" class="form-control" value="{{ old('land_area', $crop->land_area ?? '') }}" required>
    </div>

    <div class="col-md-4">
        <label class="form-label font-weight-bold">{{ $isArabic ? 'وحدة الإنتاجية' : 'Yield Unit' }} <span class="text-danger">*</span></label>
        <select name="yield_unit" id="yieldUnitSelect" class="form-select" required>
            <option value="ton" @selected($currentUnit === 'ton')>{{ $isArabic ? 'طن' : 'Ton' }}</option>
            <option value="kg" @selected($currentUnit === 'kg')>{{ $isArabic ? 'كيلوجرام (كجم)' : 'Kilogram (kg)' }}</option>
        </select>
    </div>

    <div class="col-md-4">
        <label class="form-label font-weight-bold">{{ __('crops_feed.fields.irrigation_type') }}</label>
        <select name="irrigation_type" class="form-select">
            <option value="">{{ __('crops_feed.options.none') }}</option>
            @foreach (['towers', 'seedlings', 'ground'] as $type)
                <option value="{{ $type }}" @selected(old('irrigation_type', $crop->irrigation_type ?? '') === $type)>{{ __('crops_feed.options.' . $type) }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-4">
        <label class="form-label font-weight-bold">{{ __('crops_feed.fields.greenhouse_type') }}</label>
        <input type="text" name="greenhouse_type" class="form-control" value="{{ old('greenhouse_type', $crop->greenhouse_type ?? '') }}">
    </div>

    <div class="col-md-4">
        <label class="form-label font-weight-bold">{{ __('crops_feed.fields.greenhouse_number') }}</label>
        <input type="text" name="greenhouse_number" class="form-control" value="{{ old('greenhouse_number', $crop->greenhouse_number ?? '') }}">
    </div>

    <div class="col-md-4">
        <label class="form-label font-weight-bold">{{ __('crops_feed.fields.greenhouse_location') }}</label>
        <input type="text" name="greenhouse_location" class="form-control" value="{{ old('greenhouse_location', $crop->greenhouse_location ?? '') }}">
    </div>

    <div class="col-md-6">
        <label class="form-label font-weight-bold">{{ __('crops_feed.fields.planting_date') }} <span class="text-danger">*</span></label>
        <input type="date" name="planting_date" class="form-control" value="{{ old('planting_date', isset($crop) && $crop->planting_date ? $crop->planting_date->format('Y-m-d') : '') }}" required>
    </div>

    <div class="col-md-6">
        <label class="form-label font-weight-bold">{{ __('crops_feed.fields.expected_harvest_date') }}</label>
        <input type="date" name="expected_harvest_date" class="form-control" value="{{ old('expected_harvest_date', isset($crop) && $crop->expected_harvest_date ? $crop->expected_harvest_date->format('Y-m-d') : '') }}">
    </div>

    {{-- الحقول المعتمدة على وحدة القياس --}}
    <div class="col-md-6">
        <label class="form-label font-weight-bold" id="labelYieldTons">
            {{ $isArabic ? 'كمية الإنتاجية' : 'Production Yield' }} (<span class="unit-text">{{ $currentUnit === 'kg' ? ($isArabic ? 'كجم' : 'kg') : ($isArabic ? 'طن' : 'ton') }}</span>)
        </label>
        <input type="number" step="0.01" min="0" name="yield_tons" class="form-control" value="{{ old('yield_tons', $crop->yield_tons ?? '') }}">
    </div>

    <div class="col-md-6">
        <label class="form-label font-weight-bold" id="labelWastedTons">
            {{ $isArabic ? 'الكمية التالفة / الهالك' : 'Wasted Yield' }} (<span class="unit-text">{{ $currentUnit === 'kg' ? ($isArabic ? 'كجم' : 'kg') : ($isArabic ? 'طن' : 'ton') }}</span>)
        </label>
        <input type="number" step="0.01" min="0" name="wasted_tons" class="form-control" value="{{ old('wasted_tons', $crop->wasted_tons ?? '') }}">
    </div>

    <div class="col-md-6">
        <label class="form-label font-weight-bold" id="labelAvailableFeed">
            {{ $isArabic ? 'المتاح لتغذية الأعلاف' : 'Available for Feed' }} (<span class="unit-text">{{ $currentUnit === 'kg' ? ($isArabic ? 'كجم' : 'kg') : ($isArabic ? 'طن' : 'ton') }}</span>)
        </label>
        <input type="number" step="0.01" min="0" name="available_for_feed_tons" class="form-control" value="{{ old('available_for_feed_tons', $crop->available_for_feed_tons ?? '') }}">
    </div>

    <div class="col-md-6">
        <label class="form-label font-weight-bold" id="labelSalePrice">
            {{ $isArabic ? 'سعر البيع لكل' : 'Sale Price per' }} <span class="unit-full-text">{{ $currentUnit === 'kg' ? ($isArabic ? 'كيلوجرام' : 'Kilogram') : ($isArabic ? 'طن' : 'Ton') }}</span>
        </label>
        <input type="number" step="0.01" min="0" name="sale_price_per_ton" class="form-control" value="{{ old('sale_price_per_ton', $crop->sale_price_per_ton ?? '') }}">
    </div>

    <div class="col-md-6">
        <label class="form-label font-weight-bold">{{ __('crops_feed.fields.water_cost') }}</label>
        <input type="number" step="0.01" min="0" name="water_cost" class="form-control" value="{{ old('water_cost', $crop->water_cost ?? 0) }}">
    </div>

    <div class="col-md-6">
        <label class="form-label font-weight-bold">{{ __('crops_feed.fields.labor_cost') }}</label>
        <input type="number" step="0.01" min="0" name="labor_cost" class="form-control" value="{{ old('labor_cost', $crop->labor_cost ?? 0) }}">
    </div>

    <div class="col-12">
        <label class="form-label font-weight-bold">{{ __('crops_feed.fields.notes') }}</label>
        <textarea name="notes" class="form-control" rows="3">{{ old('notes', $crop->notes ?? '') }}</textarea>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const isAr = {{ $isArabic ? 'true' : 'false' }};
        const unitSelect = document.getElementById('yieldUnitSelect');

        function updateUnitLabels() {
            if (!unitSelect) return;
            const val = unitSelect.value;
            const shortText = val === 'kg' ? (isAr ? 'كجم' : 'kg') : (isAr ? 'طن' : 'ton');
            const fullText = val === 'kg' ? (isAr ? 'كيلوجرام' : 'Kilogram') : (isAr ? 'طن' : 'Ton');

            document.querySelectorAll('.unit-text').forEach(el => el.textContent = shortText);
            document.querySelectorAll('.unit-full-text').forEach(el => el.textContent = fullText);
        }

        if (unitSelect) {
            unitSelect.addEventListener('change', updateUnitLabels);
        }
    });
</script>