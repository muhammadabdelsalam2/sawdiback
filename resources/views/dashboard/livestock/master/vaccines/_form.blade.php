@csrf
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">{{ __('livestock.fields.name') }}</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $vaccine->name ?? '') }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">{{ __('livestock.fields.default_interval_days') }}</label>
        <input type="number" min="1" name="default_interval_days" class="form-control" value="{{ old('default_interval_days', $vaccine->default_interval_days ?? '') }}">
    </div>
    <div class="col-12">
        <label class="form-label">{{ __('livestock.fields.notes') }}</label>
        <textarea name="notes" class="form-control" rows="3">{{ old('notes', $vaccine->notes ?? '') }}</textarea>
    </div>
</div>
