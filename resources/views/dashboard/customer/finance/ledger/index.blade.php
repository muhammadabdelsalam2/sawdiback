@extends('layouts.customer.dashboard')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">{{ __('finance.ledger.title') }}</h3>
    </div>

    <form class="card card-body mb-3" method="GET">
        <div class="row g-2">
            <div class="col-md-4">
                <select name="account_id" class="form-select" required>
                    <option value="">{{ __('finance.ledger.select_account') }}</option>
                    @foreach($accounts as $account)
                        <option value="{{ $account->id }}" @selected((int) request('account_id') === $account->id)>
                            {{ $account->code }} - {{ $account->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3"><input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}"></div>
            <div class="col-md-3"><input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}"></div>
            <div class="col-md-2 d-flex gap-2">
                <button class="btn btn-outline-primary w-100">{{ __('finance.common.filter') }}</button>
                <a class="btn btn-light w-100" href="{{ route('customer.finance.ledger.index', ['locale' => request()->route('locale')]) }}">{{ __('finance.common.reset') }}</a>
            </div>
        </div>
    </form>

    @if($ledgerData)
        <div class="card mb-3"><div class="card-body">
            <div class="row g-3">
                <div class="col-md-4"><strong>{{ __('finance.ledger.opening_balance') }}:</strong> {{ number_format($ledgerData['opening_balance'] ?? 0, 2) }}</div>
                <div class="col-md-4"><strong>{{ __('finance.ledger.closing_balance') }}:</strong> {{ number_format($ledgerData['closing_balance'] ?? 0, 2) }}</div>
            </div>
        </div></div>

        <div class="card"><div class="card-body">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>{{ __('finance.ledger.fields.date') }}</th>
                        <th>{{ __('finance.ledger.fields.entry_no') }}</th>
                        <th>{{ __('finance.ledger.fields.description') }}</th>
                        <th>{{ __('finance.ledger.fields.debit') }}</th>
                        <th>{{ __('finance.ledger.fields.credit') }}</th>
                        <th>{{ __('finance.ledger.fields.balance') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ledgerData['lines'] as $line)
                        <tr>
                            <td>{{ $line->entry?->entry_date?->format('Y-m-d') }}</td>
                            <td>{{ $line->entry?->entry_no }}</td>
                            <td>{{ $line->entry?->description ?? '-' }}</td>
                            <td>{{ number_format($line->debit, 2) }}</td>
                            <td>{{ number_format($line->credit, 2) }}</td>
                            <td>{{ number_format($line->running_balance ?? 0, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">{{ __('finance.ledger.empty') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
            {{ $ledgerData['lines']->links() }}
        </div></div>
    @endif
</div>
@endsection
