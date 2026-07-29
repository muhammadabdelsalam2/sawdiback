<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label">{{ __('finance.expenses.fields.expense_no') }} *</label>
        <input type="text" name="expense_no" class="form-control" value="{{ old('expense_no', $expense->expense_no ?? '') }}" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">{{ __('finance.expenses.fields.expense_date') }} *</label>
        <input type="date" name="expense_date" class="form-control" value="{{ old('expense_date', isset($expense) && $expense->expense_date ? $expense->expense_date->format('Y-m-d') : now()->toDateString()) }}" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">{{ __('finance.expenses.fields.amount') }} *</label>
        <input type="number" step="0.01" name="amount" class="form-control" value="{{ old('amount', $expense->amount ?? '') }}" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">{{ __('finance.common.farm') }}</label>
        <select name="farm_id" class="form-select">
            <option value="">{{ __('finance.common.none') }}</option>
            @foreach($farms ?? [] as $farm)
                <option value="{{ $farm->id }}" @selected(old('farm_id', $expense->farm_id ?? '') == $farm->id)>{{ $farm->name }}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="row g-3 mt-1">
    <div class="col-md-6">
        <label class="form-label">{{ __('finance.expenses.fields.expense_account') }} *</label>
        <select name="expense_account_id" class="form-select" required>
            <option value="">{{ __('finance.common.select') }}</option>
            @foreach($expenseAccounts as $account)
                <option value="{{ $account->id }}" @selected((int) old('expense_account_id', $expense->expense_account_id ?? 0) === $account->id)>
                    {{ $account->code }} - {{ $account->name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">{{ __('finance.expenses.fields.payment_account') }} *</label>
        <select name="payment_account_id" class="form-select" required>
            <option value="">{{ __('finance.common.select') }}</option>
            @foreach($paymentAccounts as $account)
                <option value="{{ $account->id }}" @selected((int) old('payment_account_id', $expense->payment_account_id ?? 0) === $account->id)>
                    {{ $account->code }} - {{ $account->name }}
                </option>
            @endforeach
        </select>
    </div>
</div>
<div class="row g-3 mt-1">
    <div class="col-md-4">
        <label class="form-label">{{ __('finance.expenses.fields.payment_method') }} *</label>
        <select name="payment_method" class="form-select" required>
            @foreach(['cash', 'bank', 'other'] as $method)
                <option value="{{ $method }}" @selected(old('payment_method', $expense->payment_method ?? 'cash') === $method)>{{ __('finance.payment_methods.' . $method) }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">{{ __('finance.expenses.fields.vendor_name') }}</label>
        <input type="text" name="vendor_name" class="form-control" value="{{ old('vendor_name', $expense->vendor_name ?? '') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">{{ __('finance.expenses.fields.status') }} *</label>
        <select name="status" class="form-select" required>
            @foreach(['draft', 'posted', 'cancelled'] as $status)
                <option value="{{ $status }}" @selected(old('status', $expense->status ?? 'posted') === $status)>{{ __('finance.expense_status.' . $status) }}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="mt-2">
    <label class="form-label">{{ __('finance.expenses.fields.notes') }}</label>
    <textarea name="notes" class="form-control" rows="2">{{ old('notes', $expense->notes ?? '') }}</textarea>
</div>
