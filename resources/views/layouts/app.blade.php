<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <link rel="stylesheet"
     href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">


    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    @stack('styles')
</head>

<body>
<div id="app">

    {{-- NAVBAR --}}
    <nav class="navbar navbar-expand-md bg-primary sticky-top app-navbar">
        <div class="container-fluid py-2">

            {{-- Offcanvas toggle --}}
            <button class="btn btn-outline-primary d-inline-flex align-items-center gap-2 rounded-pill px-3"
                    type="button"
                    data-bs-toggle="offcanvas"
                    data-bs-target="#appOffcanvas"
                    aria-controls="appOffcanvas">
                <i class="fas fa-bars"></i>
                <span class="d-none d-sm-inline">Menú</span>
            </button>

            {{-- Brand --}}
            <a class="navbar-brand ms-2 mb-0" href="{{ url('/') }}">
                <span class="brand-pill">
                    <span class="brand-dot"></span>
                    <span class="fw-semibold text-white">{{ config('app.name', 'Laravel') }}</span>
                </span>
            </a>

            {{-- Right --}}
            <div class="ms-auto d-flex align-items-center gap-2">

                @guest
                    @if (Route::has('login'))
                        <a class="btn btn-primary rounded-pill px-3" href="{{ route('login') }}">
                            Login
                        </a>
                    @endif

                    @if (Route::has('register'))
                        <a class="btn btn-outline-primary rounded-pill px-3" href="{{ route('register') }}">
                            Register
                        </a>
                    @endif
                @else
                    <div class="dropdown">
                        <button class="btn btn-light border rounded-pill px-3 dropdown-toggle nav-user"
                                type="button"
                                data-bs-toggle="dropdown"
                                aria-expanded="false">
                            <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary-subtle text-primary"
                                  style="width: 32px; height: 32px;">
                                <i class="fas fa-user"></i>
                            </span>
                            <span class="fw-semibold">{{ Auth::user()->name }}</span>
                        </button>

                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2">
                            <li>
                                <a class="dropdown-item"
                                   href="{{ route('logout') }}"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fas fa-right-from-bracket me-2"></i>
                                    Logout
                                </a>
                            </li>
                        </ul>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                @endguest
            </div>
        </div>
    </nav>


    @include('layouts.partials._side_bar')


    {{-- MAIN --}}
    <main class="app-main py-4">
        <div class="container-fluis">
            @yield('content')
        </div>
    </main>

</div>

@stack('scripts')
@yield('javas')
</body>
</html>
