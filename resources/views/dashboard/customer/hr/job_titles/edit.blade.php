@extends('layouts.customer.dashboard')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Edit Job Title</h3>
        <a class="btn btn-outline-secondary"
           href="{{ route('customer.hr.job-titles.index', ['locale' => request()->route('locale')]) }}">
            Back
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form method="POST"
                  action="{{ route('customer.hr.job-titles.update', ['locale' => request()->route('locale'), 'job_title' => $jobTitle->id]) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Name *</label>
                    <input type="text" name="name" class="form-control"
                           value="{{ old('name', $jobTitle->name) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Code</label>
                    <input type="text" name="code" class="form-control"
                           value="{{ old('code', $jobTitle->code) }}" placeholder="Optional">
                </div>

                <button class="btn btn-primary">Update</button>
                <a class="btn btn-light"
                   href="{{ route('customer.hr.job-titles.index', ['locale' => request()->route('locale')]) }}">
                    Cancel
                </a>
            </form>
        </div>
    </div>
</div>
@endsection
