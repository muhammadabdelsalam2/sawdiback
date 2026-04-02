<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label">{{ __('finance.accounts.fields.code') }} *</label>
        <input type="text" name="code" class="form-control" value="{{ old('code', $account->code ?? '') }}" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">{{ __('finance.accounts.fields.name') }} *</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $account->name ?? '') }}" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">{{ __('finance.accounts.fields.type') }} *</label>
        <select name="type" class="form-select" required>
            @foreach(['asset', 'liability', 'equity', 'revenue', 'expense'] as $type)
                <option value="{{ $type }}" @selected(old('type', $account->type ?? '') === $type)>{{ __('finance.account_types.' . $type) }}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="row g-3 mt-1">
    <div class="col-md-6">
        <label class="form-label">{{ __('finance.accounts.fields.parent') }}</label>
        <select name="parent_id" class="form-select">
            <option value="">{{ __('finance.common.none') }}</option>
            @foreach($accounts as $parent)
                <option value="{{ $parent->id }}" @selected((int) old('parent_id', $account->parent_id ?? 0) === $parent->id)>
                    {{ $parent->code }} - {{ $parent->name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">{{ __('finance.accounts.fields.active') }}</label>
        <select name="is_active" class="form-select">
            <option value="1" @selected(old('is_active', $account->is_active ?? 1) == 1)>{{ __('finance.common.active') }}</option>
            <option value="0" @selected(old('is_active', $account->is_active ?? 1) == 0)>{{ __('finance.common.inactive') }}</option>
        </select>
    </div>
</div>
