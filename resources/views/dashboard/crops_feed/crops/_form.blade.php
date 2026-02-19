@csrf
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">{{ __('crops_feed.fields.name') }}</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $crop->name ?? '') }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">{{ __('crops_feed.fields.land_area') }}</label>
        <input type="number" step="0.01" min="0.01" name="land_area" class="form-control" value="{{ old('land_area', $crop->land_area ?? '') }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">{{ __('crops_feed.fields.planting_date') }}</label>
        <input type="date" name="planting_date" class="form-control" value="{{ old('planting_date', isset($crop) && $crop->planting_date ? $crop->planting_date->format('Y-m-d') : '') }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">{{ __('crops_feed.fields.yield_tons') }}</label>
        <input type="number" step="0.01" min="0" name="yield_tons" class="form-control" value="{{ old('yield_tons', $crop->yield_tons ?? '') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">{{ __('crops_feed.fields.available_for_feed_tons') }}</label>
        <input type="number" step="0.01" min="0" name="available_for_feed_tons" class="form-control" value="{{ old('available_for_feed_tons', $crop->available_for_feed_tons ?? '') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">{{ __('crops_feed.fields.sale_price_per_ton') }}</label>
        <input type="number" step="0.01" min="0" name="sale_price_per_ton" class="form-control" value="{{ old('sale_price_per_ton', $crop->sale_price_per_ton ?? '') }}">
    </div>
    <div class="col-12">
        <label class="form-label">{{ __('crops_feed.fields.notes') }}</label>
        <textarea name="notes" class="form-control" rows="3">{{ old('notes', $crop->notes ?? '') }}</textarea>
    </div>
</div>
