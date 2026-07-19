@extends('layouts.customer.dashboard')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">{{ __('hr.titles.employees') }}</h3>
        <div class="d-flex gap-2">
            <a class="btn btn-outline-warning"
               href="{{ route('customer.hr.employees.document-alerts', ['locale' => request()->route('locale')]) }}">
                {{ __('hr.actions.document_alerts') }}
            </a>
            <a class="btn btn-primary"
               href="{{ route('customer.hr.employees.create', ['locale' => request()->route('locale')]) }}">
                {{ __('hr.actions.add_employee') }}
            </a>
        </div>
    </div>

    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th style="width:80px">{{ __('hr.fields.id') }}</th>
                        <th>{{ __('hr.fields.full_name') }}</th>
                        <th>{{ __('hr.fields.worker_number') }}</th>
                        <th>{{ __('hr.fields.operational_department') }}</th>
                        <th>{{ __('hr.fields.profession') }}</th>
                        <th>{{ __('hr.fields.iqama_expiry_date') }}</th>
                        <th>{{ __('hr.fields.employment_status') }}</th>
                        <th class="text-end" style="width:220px">{{ __('hr.fields.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $e)
                        <tr>
                            <td>{{ $e->id }}</td>
                            <td>
                                <div class="fw-semibold">{{ $e->full_name }}</div>
                                <div class="text-muted small">{{ $e->email ?? '-' }}</div>
                            </td>
                            <td>{{ $e->worker_number ?? '-' }}</td>
                            <td>{{ $e->operational_department ? __('hr.options.' . $e->operational_department) : '-' }}</td>
                            <td>{{ $e->profession ?? $e->jobTitle?->name ?? '-' }}</td>
                            <td>{{ $e->iqama_expiry_date?->format('Y-m-d') ?? '-' }}</td>
                            <td>{{ __('hr.options.' . $e->employment_status) }}</td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-outline-primary"
                                   href="{{ route('customer.hr.employees.show', ['locale' => request()->route('locale'), 'employee' => $e->id]) }}">
                                    {{ __('hr.actions.view') }}
                                </a>
                                <a class="btn btn-sm btn-outline-secondary"
                                   href="{{ route('customer.hr.employees.edit', ['locale' => request()->route('locale'), 'employee' => $e->id]) }}">
                                    {{ __('hr.actions.edit') }}
                                </a>

                                <form class="d-inline"
                                      method="POST"
                                      action="{{ route('customer.hr.employees.destroy', ['locale' => request()->route('locale'), 'employee' => $e->id]) }}"
                                      onsubmit="return confirm('{{ __('hr.messages.confirm_delete_employee') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">
                                        {{ __('hr.actions.delete') }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                {{ __('hr.empty.no_employees') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-3">
                {{ $employees->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
