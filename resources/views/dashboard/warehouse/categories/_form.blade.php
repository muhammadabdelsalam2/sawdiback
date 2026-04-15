@csrf
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">{{ __('warehouse.fields.name') }}</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $category->name ?? '') }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">{{ __('warehouse.fields.code') }}</label>
        <input type="text" name="code" class="form-control" value="{{ old('code', $category->code ?? '') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">{{ __('warehouse.fields.sort_order') }}</label>
        <input type="number" min="0" name="sort_order" class="form-control" value="{{ old('sort_order', $category->sort_order ?? 0) }}">
    </div>
    <div class="col-md-6 d-flex align-items-end">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active"
                @checked((bool) old('is_active', $category->is_active ?? true))>
            <label class="form-check-label" for="is_active">{{ __('warehouse.fields.active') }}</label>
        </div>
    </div>
    <div class="col-12">
        <label class="form-label">{{ __('warehouse.fields.notes') }}</label>
        <textarea name="notes" class="form-control" rows="3">{{ old('notes', $category->notes ?? '') }}</textarea>
    </div>
</div>
