<div class="mb-3">
    <label class="form-label">{{ __('procurement.suppliers.fields.name') }} *</label>
    <input type="text" name="name" class="form-control" value="{{ old('name', $supplier->name ?? '') }}" required>
</div>
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">{{ __('procurement.suppliers.fields.email') }}</label>
        <input type="email" name="email" class="form-control" value="{{ old('email', $supplier->email ?? '') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">{{ __('procurement.suppliers.fields.phone') }}</label>
        <input type="text" name="phone" class="form-control" value="{{ old('phone', $supplier->phone ?? '') }}">
    </div>
</div>
<div class="row g-3 mt-1">
    <div class="col-md-8">
        <label class="form-label">{{ __('procurement.suppliers.fields.address') }}</label>
        <input type="text" name="address" class="form-control" value="{{ old('address', $supplier->address ?? '') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">{{ __('procurement.suppliers.fields.status') }}</label>
        <select name="is_active" class="form-select">
            <option value="1" @selected(old('is_active', $supplier->is_active ?? true))>{{ __('procurement.status.supplier.active') }}</option>
            <option value="0" @selected(old('is_active', $supplier->is_active ?? true) === false)>{{ __('procurement.status.supplier.inactive') }}</option>
        </select>
    </div>
</div>
