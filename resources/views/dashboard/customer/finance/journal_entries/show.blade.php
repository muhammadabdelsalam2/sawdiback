@extends('layouts.customer.dashboard')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">{{ __('finance.journal_entries.show_title') }}</h3>
        <a class="btn btn-outline-secondary" href="{{ route('customer.finance.journal-entries.index', ['locale' => request()->route('locale')]) }}">{{ __('finance.common.back') }}</a>
    </div>

    <div class="card mb-3"><div class="card-body">
        <div class="row g-3">
            <div class="col-md-3"><strong>{{ __('finance.journal_entries.fields.entry_no') }}:</strong> {{ $journalEntry->entry_no }}</div>
            <div class="col-md-3"><strong>{{ __('finance.journal_entries.fields.entry_date') }}:</strong> {{ $journalEntry->entry_date?->format('Y-m-d') }}</div>
            <div class="col-md-6"><strong>{{ __('finance.journal_entries.fields.description') }}:</strong> {{ $journalEntry->description ?? '-' }}</div>
        </div>
    </div></div>

    <div class="card"><div class="card-body">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>{{ __('finance.journal_entries.lines.account') }}</th>
                    <th>{{ __('finance.journal_entries.lines.debit') }}</th>
                    <th>{{ __('finance.journal_entries.lines.credit') }}</th>
                    <th>{{ __('finance.journal_entries.lines.memo') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($journalEntry->lines as $line)
                    <tr>
                        <td>{{ $line->account->code }} - {{ $line->account->name }}</td>
                        <td>{{ number_format($line->debit, 2) }}</td>
                        <td>{{ number_format($line->credit, 2) }}</td>
                        <td>{{ $line->memo ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div></div>
</div>
@endsection
