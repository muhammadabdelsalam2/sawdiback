@extends('layouts.customer.dashboard')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Employees</h3>
        <a class="btn btn-primary"
           href="{{ route('customer.hr.employees.create', ['locale' => request()->route('locale')]) }}">
            Add Employee
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

    <div class="card">
        <div class="card-body">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th style="width:80px">#</th>
                        <th>Name</th>
                        <th>Department</th>
                        <th>Job Title</th>
                        <th style="width:140px">Active</th>
                        <th class="text-end" style="width:220px">Actions</th>
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
                            <td>{{ $e->department?->name ?? '-' }}</td>
                            <td>{{ $e->jobTitle?->name ?? '-' }}</td>
                            <td>
                                @if($e->is_active)
                                    <span class="badge bg-success">Yes</span>
                                @else
                                    <span class="badge bg-secondary">No</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-outline-secondary"
                                   href="{{ route('customer.hr.employees.edit', ['locale' => request()->route('locale'), 'employee' => $e->id]) }}">
                                    Edit
                                </a>

                                <form class="d-inline"
                                      method="POST"
                                      action="{{ route('customer.hr.employees.destroy', ['locale' => request()->route('locale'), 'employee' => $e->id]) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('Delete this employee?')">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                No employees yet.
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
