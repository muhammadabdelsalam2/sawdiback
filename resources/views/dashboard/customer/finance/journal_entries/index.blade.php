@extends('layouts.customer.dashboard')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">{{ __('finance.journal_entries.title') }}</h3>
        <a class="btn btn-primary" href="{{ route('customer.finance.journal-entries.create', ['locale' => request()->route('locale')]) }}">{{ __('finance.journal_entries.add') }}</a>
    </div>

    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

    <form class="card card-body mb-3" method="GET">
        <div class="row g-2">
            <div class="col-md-4"><input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}" title="{{ __('finance.common.date_from') }}"></div>
            <div class="col-md-4"><input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}" title="{{ __('finance.common.date_to') }}"></div>
            <div class="col-md-4 d-flex gap-2">
                <button class="btn btn-outline-primary w-100">{{ __('finance.common.filter') }}</button>
                <a class="btn btn-light w-100" href="{{ route('customer.finance.journal-entries.index', ['locale' => request()->route('locale')]) }}">{{ __('finance.common.reset') }}</a>
            </div>
        </div>
    </form>

    <div class="card"><div class="card-body">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('finance.journal_entries.fields.entry_no') }}</th>
                    <th>{{ __('finance.journal_entries.fields.entry_date') }}</th>
                    <th>{{ __('finance.journal_entries.fields.description') }}</th>
                    <th class="text-end no-sort">{{ __('finance.common.actions') }}</th>
                </tr>
            </thead>
            <tbody>
            @forelse($rows as $entry)
                <tr>
                    <td>{{ $entry->id }}</td>
                    <td>{{ $entry->entry_no }}</td>
                    <td>{{ $entry->entry_date?->format('Y-m-d') }}</td>
                    <td>{{ $entry->description ?? '-' }}</td>
                    <td class="text-end">
                        <a class="btn btn-sm btn-outline-info" href="{{ route('customer.finance.journal-entries.show', ['locale' => request()->route('locale'), 'journal_entry' => $entry->id]) }}">{{ __('finance.common.view') }}</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted py-4">{{ __('finance.journal_entries.empty') }}</td></tr>
            @endforelse
            </tbody>
        </table>
        {{ $rows->links() }}
    </div></div>
</div>
@endsection
