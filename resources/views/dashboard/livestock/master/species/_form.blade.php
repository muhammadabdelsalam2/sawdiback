@csrf
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">{{ __('livestock.fields.code') }}</label>
        <input type="text" name="code" class="form-control" value="{{ old('code', $species->code ?? '') }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">{{ __('livestock.fields.name') }}</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $species->name ?? '') }}" required>
    </div>
</div>
