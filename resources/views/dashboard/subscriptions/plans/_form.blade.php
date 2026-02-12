@csrf
<div class="mb-3">
    <label class="form-label">{{ __('subscriptions.fields.name') }}</label>
    <input type="text" name="name" class="form-control" value="{{ old('name', $plan->name ?? '') }}" required>
</div>
<div class="mb-3">
    <label class="form-label">{{ __('subscriptions.fields.slug') }}</label>
    <input type="text" name="slug" class="form-control" value="{{ old('slug', $plan->slug ?? '') }}" required>
</div>
<div class="mb-3">
    <label class="form-label">{{ __('subscriptions.fields.price') }}</label>
    <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price', $plan->price ?? '') }}" required>
</div>
<div class="mb-3">
    <label class="form-label">{{ __('subscriptions.fields.currency') }}</label>
    <select name="currency_id" class="form-select" required>
        @foreach ($currencies as $currency)
            <option value="{{ $currency->id }}"
                @selected((int) old('currency_id', $plan->currency_id ?? 0) === $currency->id)>
                {{ $currency->code }} ({{ $currency->symbol }})
            </option>
        @endforeach
    </select>
</div>
<div class="mb-3">
    <label class="form-label">{{ __('subscriptions.fields.billing_cycle') }}</label>
    <select name="billing_cycle" class="form-select" required>
        <option value="weekly" @selected(old('billing_cycle', $plan->billing_cycle ?? '') === 'weekly')>{{ __('subscriptions.billing_cycles.weekly') }}</option>
        <option value="monthly" @selected(old('billing_cycle', $plan->billing_cycle ?? '') === 'monthly')>{{ __('subscriptions.billing_cycles.monthly') }}</option>
        <option value="yearly" @selected(old('billing_cycle', $plan->billing_cycle ?? '') === 'yearly')>{{ __('subscriptions.billing_cycles.yearly') }}</option>
    </select>
</div>
<div class="mb-3">
    <label class="form-label">{{ __('subscriptions.fields.sort_order') }}</label>
    <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $plan->sort_order ?? 0) }}">
</div>
<div class="mb-3">
    <label class="form-label">{{ __('subscriptions.fields.description') }}</label>
    <textarea name="description" class="form-control" rows="3">{{ old('description', $plan->description ?? '') }}</textarea>
</div>
<div class="form-check mb-3">
    <input type="hidden" name="is_active" value="0">
    <input class="form-check-input" type="checkbox" name="is_active" value="1"
        @checked(old('is_active', $plan->is_active ?? true))>
    <label class="form-check-label">{{ __('subscriptions.fields.is_active') }}</label>
</div>
<button class="btn btn-success" type="submit">{{ __('subscriptions.actions.save') }}</button>
