<!DOCTYPE html>
<html lang="{{ $currentLang }}" dir="{{ $direction }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'My SaaS')</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="{{ asset('assets/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/bootstrap/css/bootstrap.rtl.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/pages/dashboard.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Main CSS -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    @stack('styles')
</head>

<body  dir="{{ $direction }}">

    @include('shared.dashboard.navbar')

    <main>
        <div class="wrapper grow w-100">
            @include('shared.dashboard.sidebar')
            <main id="content">
                @yield('content')
            </main>

        </div>
    </main>

    {{-- @include('shared.dashboard.footer') --}}

    <!-- Bootstrap JS -->
    <script src="{{ asset('assets/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Custom JS -->
    <script src="{{ asset('assets/js/pages/dashboard.js') }}"></script>

    @stack('scripts')
</body>

</html>