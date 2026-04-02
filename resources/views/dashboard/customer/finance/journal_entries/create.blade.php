@extends('layouts.customer.dashboard')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">{{ __('finance.journal_entries.create_title') }}</h3>
        <a class="btn btn-outline-secondary" href="{{ route('customer.finance.journal-entries.index', ['locale' => request()->route('locale')]) }}">{{ __('finance.common.back') }}</a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul></div>
    @endif

    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('customer.finance.journal-entries.store', ['locale' => request()->route('locale')]) }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">{{ __('finance.journal_entries.fields.entry_date') }} *</label>
                    <input type="date" name="entry_date" class="form-control" value="{{ old('entry_date', now()->toDateString()) }}" required>
                </div>
                <div class="col-md-8">
                    <label class="form-label">{{ __('finance.journal_entries.fields.description') }}</label>
                    <input type="text" name="description" class="form-control" value="{{ old('description') }}">
                </div>
            </div>

            <div class="mt-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0">{{ __('finance.journal_entries.lines.title') }}</h6>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="add-line">{{ __('finance.journal_entries.lines.add') }}</button>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle" id="lines-table">
                        <thead>
                            <tr>
                                <th>{{ __('finance.journal_entries.lines.account') }}</th>
                                <th>{{ __('finance.journal_entries.lines.debit') }}</th>
                                <th>{{ __('finance.journal_entries.lines.credit') }}</th>
                                <th>{{ __('finance.journal_entries.lines.memo') }}</th>
                                <th class="text-end">{{ __('finance.common.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @for($i = 0; $i < 2; $i++)
                                <tr>
                                    <td>
                                        <select name="lines[{{ $i }}][account_id]" class="form-select" required>
                                            <option value="">{{ __('finance.common.select') }}</option>
                                            @foreach($accounts as $account)
                                                <option value="{{ $account->id }}" @selected(old("lines.$i.account_id") == $account->id)>
                                                    {{ $account->code }} - {{ $account->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td><input type="number" step="0.01" name="lines[{{ $i }}][debit]" class="form-control" value="{{ old("lines.$i.debit") }}"></td>
                                    <td><input type="number" step="0.01" name="lines[{{ $i }}][credit]" class="form-control" value="{{ old("lines.$i.credit") }}"></td>
                                    <td><input type="text" name="lines[{{ $i }}][memo]" class="form-control" value="{{ old("lines.$i.memo") }}"></td>
                                    <td class="text-end"><button type="button" class="btn btn-sm btn-outline-danger remove-line">{{ __('finance.common.delete') }}</button></td>
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
            </div>

            <button class="btn btn-primary">{{ __('finance.common.save') }}</button>
            <a class="btn btn-light" href="{{ route('customer.finance.journal-entries.index', ['locale' => request()->route('locale')]) }}">{{ __('finance.common.cancel') }}</a>
        </form>
    </div></div>
</div>
@endsection

@push('scripts')
<script>
    (function () {
        const tableBody = document.querySelector('#lines-table tbody');
        const addButton = document.getElementById('add-line');

        if (!tableBody || !addButton) return;

        function nextIndex() {
            return tableBody.querySelectorAll('tr').length;
        }

        function bindRemove(btn) {
            btn.addEventListener('click', function () {
                const row = btn.closest('tr');
                if (row && tableBody.querySelectorAll('tr').length > 1) {
                    row.remove();
                }
            });
        }

        tableBody.querySelectorAll('.remove-line').forEach(bindRemove);

        addButton.addEventListener('click', function () {
            const idx = nextIndex();
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>
                    <select name="lines[${idx}][account_id]" class="form-select" required>
                        <option value="">{{ __('finance.common.select') }}</option>
                        @foreach($accounts as $account)
                            <option value="{{ $account->id }}">{{ $account->code }} - {{ $account->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td><input type="number" step="0.01" name="lines[${idx}][debit]" class="form-control"></td>
                <td><input type="number" step="0.01" name="lines[${idx}][credit]" class="form-control"></td>
                <td><input type="text" name="lines[${idx}][memo]" class="form-control"></td>
                <td class="text-end"><button type="button" class="btn btn-sm btn-outline-danger remove-line">{{ __('finance.common.delete') }}</button></td>
            `;
            tableBody.appendChild(row);
            bindRemove(row.querySelector('.remove-line'));
        });
    })();
</script>
@endpush
