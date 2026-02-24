@extends('layouts.customer.dashboard')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Attendance</h3>
        <a class="btn btn-outline-secondary"
           href="{{ route('customer.hr.employees.index', ['locale' => request()->route('locale')]) }}">
            Employees
        </a>
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

    <div class="card mb-3">
        <div class="card-body">
            <form method="POST"
                  action="{{ route('customer.hr.attendance.checkin', ['locale' => request()->route('locale')]) }}"
                  class="row g-2 align-items-end">
                @csrf
                <div class="col-md-6">
                    <label class="form-label">Employee *</label>
                    <select name="employee_id" class="form-select" required>
                        <option value="">-- Select Employee --</option>
                        @foreach($employees as $e)
                            <option value="{{ $e->id }}">{{ $e->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 text-end">
                    <button class="btn btn-primary">Check In</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Day</th>
                        <th>Employee</th>
                        <th>Check In</th>
                        <th>Check Out</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $a)
                        <tr>
                            <td>{{ optional($a->day)->format('Y-m-d') }}</td>
                            <td>{{ $a->employee?->full_name ?? '-' }}</td>
                            <td>{{ $a->check_in_at ? $a->check_in_at->format('Y-m-d H:i') : '-' }}</td>
                            <td>{{ $a->check_out_at ? $a->check_out_at->format('Y-m-d H:i') : '-' }}</td>
                            <td class="text-end">
                                @if(!$a->check_out_at)
                                    <form method="POST"
                                          action="{{ route('customer.hr.attendance.checkout', ['locale' => request()->route('locale'), 'attendance' => $a->id]) }}">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-success"
                                                onclick="return confirm('Check out now?')">
                                            Check Out
                                        </button>
                                    </form>
                                @else
                                    <span class="text-muted small">Done</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                No attendance records.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-3">
                {{ $attendances->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
