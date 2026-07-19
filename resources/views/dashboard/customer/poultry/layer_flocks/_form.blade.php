@csrf
<div class="row g-3">
    <div class="col-md-3"><label class="form-label">{{ __('poultry.fields.flock_number') }}</label><input type="text" name="flock_number" class="form-control" value="{{ old('flock_number', $flock->flock_number ?? '') }}" required></div>
    <div class="col-md-2"><label class="form-label">{{ __('poultry.fields.pen_id') }}</label><select name="pen_id" class="form-select"><option value="">{{ __('farms.empty.no_pens') }}</option>@foreach($pens ?? [] as $pen)<option value="{{ $pen->id }}" @selected(old('pen_id', $flock->pen_id ?? '') == $pen->id)>{{ $pen->farm?->name }} - {{ $pen->pen_number }}</option>@endforeach</select></div>
    <div class="col-md-2"><label class="form-label">{{ __('poultry.fields.chicken_count') }}</label><input type="number" min="1" name="chicken_count" class="form-control" value="{{ old('chicken_count', $flock->chicken_count ?? '') }}" required></div>
    <div class="col-md-2"><label class="form-label">{{ __('poultry.fields.purchase_cost') }}</label><input type="number" step="0.01" min="0" name="purchase_cost" class="form-control" value="{{ old('purchase_cost', $flock->purchase_cost ?? '') }}" required></div>
    <div class="col-md-3"><label class="form-label">{{ __('poultry.fields.started_at') }}</label><input type="date" name="started_at" class="form-control" value="{{ old('started_at', isset($flock) ? $flock->started_at?->format('Y-m-d') : '') }}" required></div>
    <div class="col-md-3"><label class="form-label">{{ __('poultry.fields.status') }}</label><select name="status" class="form-select">@foreach(['active', 'finished'] as $status)<option value="{{ $status }}" @selected(old('status', $flock->status ?? 'active') === $status)>{{ __('poultry.options.' . $status) }}</option>@endforeach</select></div>
    <div class="col-md-9"><label class="form-label">{{ __('poultry.fields.notes') }}</label><input type="text" name="notes" class="form-control" value="{{ old('notes', $flock->notes ?? '') }}"></div>
</div>
