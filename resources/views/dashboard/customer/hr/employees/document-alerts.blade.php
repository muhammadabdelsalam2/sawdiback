@extends('layouts.customer.dashboard')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="mb-0">{{ __('hr.titles.document_alerts') }}</h3>
            <div class="text-muted small">{{ __('hr.messages.document_alert_window', ['days' => $days]) }}</div>
        </div>
        <a class="btn btn-outline-secondary"
           href="{{ route('customer.hr.employees.index', ['locale' => request()->route('locale')]) }}">
            {{ __('hr.actions.back') }}
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>{{ __('hr.fields.employee') }}</th>
                        <th>{{ __('hr.fields.worker_number') }}</th>
                        <th>{{ __('hr.fields.operational_department') }}</th>
                        <th>{{ __('hr.fields.passport_expiry_date') }}</th>
                        <th>{{ __('hr.fields.iqama_expiry_date') }}</th>
                        <th>{{ __('hr.fields.expiring_documents') }}</th>
                        <th class="text-end">{{ __('hr.fields.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $row)
                        @php($employee = $row['employee'])
                        <tr>
                            <td>{{ $employee->full_name }}</td>
                            <td>{{ $employee->worker_number ?? '-' }}</td>
                            <td>{{ $employee->operational_department ? __('hr.options.' . $employee->operational_department) : '-' }}</td>
                            <td>{{ $employee->passport_expiry_date?->format('Y-m-d') ?? '-' }}</td>
                            <td>{{ $employee->iqama_expiry_date?->format('Y-m-d') ?? '-' }}</td>
                            <td>
                                @if($row['passport_expiring'])
                                    <span class="badge bg-warning text-dark">{{ __('hr.attachments.passport') }}</span>
                                @endif
                                @if($row['iqama_expiring'])
                                    <span class="badge bg-warning text-dark">{{ __('hr.attachments.iqama') }}</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-outline-primary"
                                   href="{{ route('customer.hr.employees.show', ['locale' => request()->route('locale'), 'employee' => $employee->id]) }}">
                                    {{ __('hr.actions.view') }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                {{ __('hr.empty.no_document_alerts') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
