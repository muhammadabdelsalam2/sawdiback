@props([
    'action' => '#', // Form action URL
    'method' => 'POST', // HTTP method
    'files' => false, // File upload support
    'axios' => false, // Enable Axios/jQuery AJAX submission
    'id' => 'form-' . uniqid(), // Unique form ID
])

<form
    id="{{ $id }}"
    action="{{ $axios ? '#' : $action }}"
    method="{{ strtoupper($method) === 'GET' ? 'GET' : 'POST' }}"
    {{ $files ? 'enctype=multipart/form-data' : '' }}
    {{ $attributes->merge(['class' => 'row g-3 needs-validation']) }}
    novalidate
    @if($axios) 
        data-axios="true" 
        data-action="{{ $action }}" 
        data-method="{{ strtoupper($method) }}" 
    @endif
>

    {{--@if (strtoupper($method !== 'GET')
        @csrf
    @endif

    @if (!in_array(strtoupper($method), ['GET', 'POST']))
        @method($method)
    @endif--}}

    {{ $slot }}

    @if ($errors->any())
        <div class="alert alert-danger mt-2">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</form>


