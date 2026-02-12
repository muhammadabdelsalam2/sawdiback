@csrf
<div class="mb-3">
    <label class="form-label">{{ __('subscriptions.fields.key') }}</label>
    <input type="text" name="key" class="form-control" value="{{ old('key', $feature->key ?? '') }}" required>
</div>
<div class="mb-3">
    <label class="form-label">{{ __('subscriptions.fields.name') }}</label>
    <input type="text" name="name" class="form-control" value="{{ old('name', $feature->name ?? '') }}" required>
</div>
<div class="mb-3">
    <label class="form-label">{{ __('subscriptions.fields.type') }}</label>
    <select name="type" class="form-select" required>
        <option value="boolean" @selected(old('type', $feature->type ?? '') === 'boolean')>boolean</option>
        <option value="number" @selected(old('type', $feature->type ?? '') === 'number')>number</option>
        <option value="string" @selected(old('type', $feature->type ?? '') === 'string')>string</option>
    </select>
</div>
<div class="mb-3">
    <label class="form-label">{{ __('subscriptions.fields.description') }}</label>
    <textarea name="description" class="form-control" rows="3">{{ old('description', $feature->description ?? '') }}</textarea>
</div>
<div class="form-check mb-3">
    <input type="hidden" name="is_active" value="0">
    <input class="form-check-input" type="checkbox" name="is_active" value="1"
        @checked(old('is_active', $feature->is_active ?? true))>
    <label class="form-check-label">{{ __('subscriptions.fields.is_active') }}</label>
</div>
<button class="btn btn-success" type="submit">{{ __('subscriptions.actions.save') }}</button>
