@extends('layouts.customer.dashboard')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Leave Requests</h3>
        <a class="btn btn-primary"
           href="{{ route('customer.hr.leaves.create', ['locale' => request()->route('locale')]) }}">
            New Request
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
                        <th>#</th>
                        <th>Employee</th>
                        <th>Type</th>
                        <th>From</th>
                        <th>To</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leaves as $l)
                        <tr>
                            <td>{{ $l->id }}</td>
                            <td>{{ $l->employee?->full_name ?? '-' }}</td>
                            <td>{{ $l->type }}</td>
                            <td>{{ $l->start_date?->format('Y-m-d') }}</td>
                            <td>{{ $l->end_date?->format('Y-m-d') }}</td>
                            <td>
                                @if($l->status === 'approved')
                                    <span class="badge bg-success">Approved</span>
                                @elseif($l->status === 'rejected')
                                    <span class="badge bg-danger">Rejected</span>
                                @else
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @endif
                            </td>
                            <td class="text-end">
                                @if($l->status === 'pending')
                                    <form class="d-inline" method="POST"
                                          action="{{ route('customer.hr.leaves.approve', ['locale' => request()->route('locale'), 'leave' => $l->id]) }}">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-success"
                                                onclick="return confirm('Approve this request?')">
                                            Approve
                                        </button>
                                    </form>

                                    <form class="d-inline" method="POST"
                                          action="{{ route('customer.hr.leaves.reject', ['locale' => request()->route('locale'), 'leave' => $l->id]) }}">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('Reject this request?')">
                                            Reject
                                        </button>
                                    </form>
                                @else
                                    <span class="text-muted small">No actions</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                No leave requests.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-3">
                {{ $leaves->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
