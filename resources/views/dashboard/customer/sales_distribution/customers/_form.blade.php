<div class="mb-3">
    <label class="form-label">{{ __('sales_dist.customers.fields.name') }} *</label>
    <input type="text" name="name" class="form-control" value="{{ old('name', $customer->name ?? '') }}" required>
</div>
<div class="mb-3">
    <label class="form-label">{{ __('sales_dist.customers.fields.type') }} *</label>
    <select name="type" class="form-select" required>
        @foreach (['trader', 'factory', 'shop'] as $value)
            <option value="{{ $value }}" @selected(old('type', $customer->type ?? '') === $value)>{{ __("sales_dist.status.types.$value") }}</option>
        @endforeach
    </select>
</div>
<div class="mb-3">
    <label class="form-label">{{ __('sales_dist.customers.fields.phones') }} *</label>
    <input type="text" name="phones" class="form-control" value="{{ old('phones', $customer->phones ?? '') }}" required>
</div>
<div class="mb-3">
    <label class="form-label">{{ __('sales_dist.customers.fields.address') }} *</label>
    <input type="text" name="address" class="form-control" value="{{ old('address', $customer->address ?? '') }}" required>
</div>
<div class="mb-3">
    <label class="form-label">{{ __('sales_dist.customers.fields.tax_number') }}</label>
    <input type="text" name="tax_number" class="form-control" value="{{ old('tax_number', $customer->tax_number ?? '') }}">
</div>
<div class="mb-3">
    <label class="form-label">{{ __('sales_dist.customers.fields.status') }} *</label>
    <select name="status" class="form-select" required>
        @foreach (['active', 'inactive'] as $value)
            <option value="{{ $value }}" @selected(old('status', $customer->status ?? 'active') === $value)>{{ __("sales_dist.status.customer.$value") }}</option>
        @endforeach
    </select>
</div>
<div class="mb-3">
    <label class="form-label">{{ __('sales_dist.customers.fields.notes') }}</label>
    <textarea name="notes" class="form-control" rows="3">{{ old('notes', $customer->notes ?? '') }}</textarea>
</div>
