<div class="row g-3">


    <div class="col-md-4">
        <label class="form-label">{{ __('warehouse.fields.code') }}</label>
        <input type="text" name="code" class="form-control" value="{{ old('code', $product->code ?? '') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">{{ __('warehouse.fields.name') }}</label>
        <input type="text" name="name" class="form-control" required
            value="{{ old('name', $product->name ?? '') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">{{ __('warehouse.fields.title_ar') }}</label>
        <input type="text" name="title_ar" class="form-control"
            value="{{ old('title_ar', is_array($product->title ?? null) ? ($product->title['ar'] ?? '') : '') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">{{ __('warehouse.fields.title_en') }}</label>
        <input type="text" name="title_en" class="form-control"
            value="{{ old('title_en', is_array($product->title ?? null) ? ($product->title['en'] ?? '') : '') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">{{ __('warehouse.fields.category') }}</label>
        <select name="category_id" class="form-select" required>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected((int) old('category_id', $product->category_id ?? 0) === $category->id)>
                    {{ $category->name ?? $category->code }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">{{ __('warehouse.fields.asset_category') }}</label>
        <select name="asset_category" class="form-select" required>
            @foreach(['feed', 'seed', 'equipment', 'other'] as $assetCategory)
                <option value="{{ $assetCategory }}" @selected(old('asset_category', $product->asset_category ?? 'other') === $assetCategory)>
                    {{ __('warehouse.asset_categories.' . $assetCategory) }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">{{ __('warehouse.fields.image') }}</label>
        @if (!empty($product->image))
            <div class="mb-2">
                <img src="{{ $product->image_url }}"
                    onerror="this.onerror=null;this.src='{{ $product->placeholder_image_url }}';"
                    style="width:80px; height:80px; border-radius:8px; object-fit:cover;">
            </div>
        @endif
        <input type="file" name="image" class="form-control" accept="image/*">
    </div>
    <div class="col-md-3">
        <label class="form-label">{{ __('warehouse.fields.price') }}</label>
        <input type="number" step="0.01" min="0" name="price" class="form-control"
            value="{{ old('price', $product->price ?? 0) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label">{{ __('warehouse.fields.last_price') }}</label>
        <input type="number" step="0.01" min="0" name="last_price" class="form-control"
            value="{{ old('last_price', $product->last_price ?? 0) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label">{{ __('warehouse.fields.unit') }}</label>
        <input type="text" name="unit" class="form-control" required
            value="{{ old('unit', $product->unit ?? '') }}">
    </div>
    <div class="col-md-3">
        <label class="form-label">{{ __('warehouse.fields.tax') }}</label>
        <input type="number" step="0.01" min="0" name="tax" class="form-control"
            value="{{ old('tax', $product->tax ?? 0) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label">{{ __('warehouse.fields.low_stock_threshold') }}</label>
        <input type="number" step="0.01" min="0" name="low_stock_threshold" class="form-control"
            value="{{ old('low_stock_threshold', $product->low_stock_threshold ?? 0) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label">{{ __('warehouse.fields.farm') }}</label>
        <select name="farm_id" class="form-select">
            <option value="">{{ __('warehouse.fields.select_farm') }}</option>
            @foreach($farms ?? [] as $farm)
                <option value="{{ $farm->id }}" @selected((int) old('farm_id', $product->farm_id ?? 0) === $farm->id)>{{ $farm->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label">{{ __('warehouse.fields.farm_location') }}</label>
        <input type="text" name="farm_location" class="form-control"
            value="{{ old('farm_location', $product->farm_location ?? '') }}">
    </div>
    <div class="col-md-3 d-flex align-items-end">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="track_expiry" value="1" id="track_expiry"
                @checked((bool) old('track_expiry', $product->track_expiry ?? false))>
            <label class="form-check-label" for="track_expiry">{{ __('warehouse.fields.track_expiry') }}</label>
        </div>
    </div>
    <div class="col-md-3 d-flex align-items-end">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active"
                @checked((bool) old('is_active', $product->is_active ?? true))>
            <label class="form-check-label" for="is_active">{{ __('warehouse.fields.active') }}</label>
        </div>
    </div>
    <div class="col-md-3 d-flex align-items-end">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="is_best_selling" value="1" id="is_best_selling"
                @checked((bool) old('is_best_selling', $product->is_best_selling ?? false))>
            <label class="form-check-label text-warning fw-bold"
                for="is_best_selling">{{ __('warehouse.fields.best_selling') }}</label>
        </div>
    </div>
    <div class="col-12">
        <label class="form-label">{{ __('warehouse.fields.notes') }}</label>
        <textarea name="notes" class="form-control" rows="3">{{ old('notes', $product->notes ?? '') }}</textarea>
    </div>
    <div class="col-md-6">
        <label class="form-label">{{ __('warehouse.fields.description_ar') }}</label>
        <textarea name="description_ar" class="form-control" rows="3">{{ old('description_ar', is_array($product->description ?? null) ? ($product->description['ar'] ?? '') : '') }}</textarea>
    </div>
    <div class="col-md-6">
        <label class="form-label">{{ __('warehouse.fields.description_en') }}</label>
        <textarea name="description_en" class="form-control" rows="3">{{ old('description_en', is_array($product->description ?? null) ? ($product->description['en'] ?? '') : '') }}</textarea>
    </div>
</div>
