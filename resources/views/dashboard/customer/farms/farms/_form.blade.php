@csrf
<div class="row g-3">
    <div class="col-md-4"><label class="form-label">{{ __('farms.fields.name') }}</label><input type="text" name="name" class="form-control" value="{{ old('name', $farm->name ?? '') }}" required></div>
    <div class="col-md-3"><label class="form-label">{{ __('farms.fields.type') }}</label><select name="type" class="form-select" required>@foreach(['owned','rented'] as $type)<option value="{{ $type }}" @selected(old('type', $farm->type ?? '') === $type)>{{ __('farms.options.' . $type) }}</option>@endforeach</select></div>
    <div class="col-md-3"><label class="form-label">{{ __('farms.fields.location') }}</label><input type="text" name="location" class="form-control" value="{{ old('location', $farm->location ?? '') }}"></div>
    <div class="col-md-2"><label class="form-label">{{ __('farms.fields.is_active') }}</label><select name="is_active" class="form-select"><option value="1" @selected(old('is_active', (int)($farm->is_active ?? 1)) === 1)>{{ __('farms.options.active') }}</option><option value="0" @selected(old('is_active', (int)($farm->is_active ?? 1)) === 0)>{{ __('farms.options.inactive') }}</option></select></div>
    <div class="col-12"><label class="form-label">{{ __('farms.fields.notes') }}</label><input type="text" name="notes" class="form-control" value="{{ old('notes', $farm->notes ?? '') }}"></div>
</div>
