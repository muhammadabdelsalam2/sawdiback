@csrf
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">{{ __('livestock.fields.name') }}</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $feedType->name ?? '') }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">{{ __('livestock.fields.category') }}</label>
        <select name="category" class="form-select" required>
            @foreach (['concentrate', 'roughage', 'supplement'] as $cat)
                <option value="{{ $cat }}" @selected(old('category', $feedType->category ?? '') === $cat)>{{ __('livestock.options.' . $cat) }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">{{ __('livestock.fields.unit') }}</label>
        <input type="text" name="unit" class="form-control" value="{{ old('unit', $feedType->unit ?? '') }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">{{ __('livestock.fields.cost_per_unit') }}</label>
        <input type="number" step="0.01" min="0" name="cost_per_unit" class="form-control" value="{{ old('cost_per_unit', $feedType->cost_per_unit ?? '') }}">
    </div>
    <div class="col-12">
        <label class="form-label">{{ __('livestock.fields.notes') }}</label>
        <textarea name="notes" class="form-control" rows="3">{{ old('notes', $feedType->notes ?? '') }}</textarea>
    </div>
</div>
