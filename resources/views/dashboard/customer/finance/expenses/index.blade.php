@extends('layouts.customer.dashboard')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">{{ __('finance.expenses.title') }}</h3>
        <a class="btn btn-primary" href="{{ route('customer.finance.expenses.create', ['locale' => request()->route('locale')]) }}">{{ __('finance.expenses.add') }}</a>
    </div>

    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

    <form class="card card-body mb-3" method="GET">
        <div class="row g-2">
            <div class="col-md-3"><input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}"></div>
            <div class="col-md-3"><input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}"></div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">{{ __('finance.common.all_status') }}</option>
                    @foreach(['draft', 'posted', 'cancelled'] as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ __('finance.expense_status.' . $status) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button class="btn btn-outline-primary w-100">{{ __('finance.common.filter') }}</button>
                <a class="btn btn-light w-100" href="{{ route('customer.finance.expenses.index', ['locale' => request()->route('locale')]) }}">{{ __('finance.common.reset') }}</a>
            </div>
        </div>
    </form>

    <div class="card"><div class="card-body">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('finance.expenses.fields.expense_no') }}</th>
                    <th>{{ __('finance.expenses.fields.expense_date') }}</th>
                    <th>{{ __('finance.expenses.fields.amount') }}</th>
                    <th>{{ __('finance.expenses.fields.status') }}</th>
                    <th class="text-end no-sort">{{ __('finance.common.actions') }}</th>
                </tr>
            </thead>
            <tbody>
            @forelse($rows as $row)
                <tr>
                    <td>{{ $row->id }}</td>
                    <td>{{ $row->expense_no }}</td>
                    <td>{{ $row->expense_date?->format('Y-m-d') }}</td>
                    <td>{{ number_format($row->amount, 2) }}</td>
                    <td>{{ __('finance.expense_status.' . $row->status) }}</td>
                    <td class="text-end">
                        <a class="btn btn-sm btn-outline-secondary" href="{{ route('customer.finance.expenses.edit', ['locale' => request()->route('locale'), 'expense' => $row->id]) }}">{{ __('finance.common.edit') }}</a>
                        <form class="d-inline" method="POST" action="{{ route('customer.finance.expenses.destroy', ['locale' => request()->route('locale'), 'expense' => $row->id]) }}">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" onclick="return confirm('{{ __('finance.expenses.confirm_delete') }}')">{{ __('finance.common.delete') }}</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-4">{{ __('finance.expenses.empty') }}</td></tr>
            @endforelse
            </tbody>
        </table>
        {{ $rows->links() }}
    </div></div>
</div>
@endsection
