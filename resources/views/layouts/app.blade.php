<!doctype html>
<html lang="es">
<head>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('page-title', 'Panel de gestión') · InsureTech Assess</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('assets/css/@fortawesome/fontawesome-free/css/all.min.css') }}">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="claims-page {{ auth()->check() ? 'has-sidebar' : '' }}">
<div class="claims-workspace">
    @auth
        @include('layouts.partials._side_bar')
    @endauth
    <main class="claims-main">
        <header class="claims-header">
            <div class="claims-heading">
                <h1>@yield('page-title', 'Panel de gestión')</h1>
                @hasSection('page-description')<p>@yield('page-description')</p>@endif
            </div>
            <div class="claims-header-tools">
                @yield('header-tools')
                @guest
                    <a href="{{ route('login') }}">Ingresar</a>
                @endguest
            </div>
        </header>
        @if(session('success'))<div class="alert alert-success" role="status">{{ session('success') }}</div>@endif
        @yield('content')
    </main>
</div>
<script src="{{ asset('assets/js/jquery.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
@stack('scripts')
@yield('javas')
</body>
</html>
