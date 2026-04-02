@extends('layouts.customer.dashboard')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">{{ __('finance.accounts.title') }}</h3>
        <a class="btn btn-primary" href="{{ route('customer.finance.accounts.create', ['locale' => request()->route('locale')]) }}">{{ __('finance.accounts.add') }}</a>
    </div>

    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

    <div class="card"><div class="card-body">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('finance.accounts.fields.code') }}</th>
                    <th>{{ __('finance.accounts.fields.name') }}</th>
                    <th>{{ __('finance.accounts.fields.type') }}</th>
                    <th>{{ __('finance.accounts.fields.parent') }}</th>
                    <th>{{ __('finance.accounts.fields.active') }}</th>
                    <th class="text-end no-sort">{{ __('finance.common.actions') }}</th>
                </tr>
            </thead>
            <tbody>
            @forelse($rows as $row)
                <tr>
                    <td>{{ $row->id }}</td>
                    <td>{{ $row->code }}</td>
                    <td>{{ $row->name }}</td>
                    <td>{{ __('finance.account_types.' . $row->type) }}</td>
                    <td>{{ $row->parent?->name ?? '-' }}</td>
                    <td>{{ $row->is_active ? __('finance.common.active') : __('finance.common.inactive') }}</td>
                    <td class="text-end">
                        <a class="btn btn-sm btn-outline-secondary" href="{{ route('customer.finance.accounts.edit', ['locale' => request()->route('locale'), 'account' => $row->id]) }}">{{ __('finance.common.edit') }}</a>
                        <form class="d-inline" method="POST" action="{{ route('customer.finance.accounts.destroy', ['locale' => request()->route('locale'), 'account' => $row->id]) }}">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" onclick="return confirm('{{ __('finance.accounts.confirm_delete') }}')">{{ __('finance.common.delete') }}</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted py-4">{{ __('finance.accounts.empty') }}</td></tr>
            @endforelse
            </tbody>
        </table>
    </div></div>
</div>
@endsection
