<div class="mb-3">
    <label class="form-label">{{ __('sales_dist.contracts.fields.customer') }} *</label>
    <select name="customer_id" class="form-select" required>
        <option value="">{{ __('sales_dist.contracts.select_customer') }}</option>
        @foreach($customers as $customerOption)
            <option value="{{ $customerOption->id }}" @selected((int) old('customer_id', $contract->customer_id ?? 0) === $customerOption->id)>{{ $customerOption->name }}</option>
        @endforeach
    </select>
</div>
<div class="mb-3">
    <label class="form-label">{{ __('sales_dist.contracts.fields.code') }} *</label>
    <input type="text" name="contract_code" class="form-control" value="{{ old('contract_code', $contract->contract_code ?? '') }}" required>
</div>
<div class="row g-3">
    <div class="col-md-6"><label class="form-label">{{ __('sales_dist.contracts.fields.start_date') }} *</label><input type="date" name="start_date" class="form-control" value="{{ old('start_date', isset($contract) && $contract->start_date ? $contract->start_date->format('Y-m-d') : '') }}" required></div>
    <div class="col-md-6"><label class="form-label">{{ __('sales_dist.contracts.fields.end_date') }}</label><input type="date" name="end_date" class="form-control" value="{{ old('end_date', isset($contract) && $contract->end_date ? $contract->end_date->format('Y-m-d') : '') }}"></div>
</div>
<div class="mb-3 mt-3">
    <label class="form-label">{{ __('sales_dist.contracts.fields.payment_terms') }} *</label>
    <input type="text" name="payment_terms" class="form-control" value="{{ old('payment_terms', $contract->payment_terms ?? '') }}" required>
</div>
<div class="mb-3">
    <label class="form-label">{{ __('sales_dist.contracts.fields.credit_limit') }}</label>
    <input type="number" step="0.01" name="credit_limit" class="form-control" value="{{ old('credit_limit', $contract->credit_limit ?? '') }}">
</div>
<div class="mb-3">
    <label class="form-label">{{ __('sales_dist.contracts.fields.status') }} *</label>
    <select name="status" class="form-select" required>
        @foreach(['active', 'inactive', 'expired'] as $value)
            <option value="{{ $value }}" @selected(old('status', $contract->status ?? 'active') === $value)>{{ __("sales_dist.status.contract.$value") }}</option>
        @endforeach
    </select>
</div>
<div class="mb-3">
    <label class="form-label">{{ __('sales_dist.contracts.fields.notes') }}</label>
    <textarea name="notes" class="form-control" rows="3">{{ old('notes', $contract->notes ?? '') }}</textarea>
</div>
