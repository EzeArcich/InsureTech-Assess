<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    @stack('styles')
</head>

<body>
<div id="app">

    {{-- NAVBAR --}}
    <nav class="navbar navbar-expand-md bg-white sticky-top app-navbar">
        <div class="container py-2">

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
                    <span class="fw-semibold">{{ config('app.name', 'Laravel') }}</span>
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

    {{-- OFFCANVAS MENU --}}
    <div class="offcanvas offcanvas-start app-offcanvas"
         tabindex="-1"
         id="appOffcanvas"
         aria-labelledby="appOffcanvasLabel">
        <div class="offcanvas-header border-bottom">
            <div>
                <h5 class="offcanvas-title mb-0" id="appOffcanvasLabel">Navegación</h5>
                <small class="text-muted">Accesos rápidos</small>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>

        <div class="offcanvas-body">

            {{-- Sección general --}}
            <div class="menu-section-title">General</div>

            <a class="menu-link {{ Request::is('home') ? 'active' : '' }}" href="{{ url('/home') }}">
                <span class="menu-icon"><i class="fas fa-table"></i></span>
                <span class="menu-text">Dashboard</span>
            </a>

            <a class="menu-link {{ Request::is('talleres') ? 'active' : '' }}" href="{{ url('/talleres') }}">
                <span class="menu-icon"><i class="fas fa-wrench"></i></span>
                <span class="menu-text">Talleres homologados</span>
            </a>

            <a class="menu-link {{ Request::is('siniestros*') ? 'active' : '' }}" href="{{ url('/siniestros') }}">
                <span class="menu-icon"><i class="fas fa-car"></i></span>
                <span class="menu-text">Siniestros</span>
            </a>

            {{-- Coordinaciones --}}
            @hasrole('Coordinaciones')
                <div class="menu-section-title mt-3">Coordinaciones</div>

                <a class="menu-link {{ Request::is('blogs') ? 'active' : '' }}" href="{{ url('/blogs') }}">
                    <span class="menu-icon"><i class="fas fa-blog"></i></span>
                    <span class="menu-text">Noticias</span>
                </a>

                <a class="menu-link {{ Request::is('siniestros/pendientes') ? 'active' : '' }}" href="{{ url('/siniestros/pendientes') }}">
                    <span class="menu-icon"><i class="fas fa-clock"></i></span>
                    <span class="menu-text">Pendientes</span>
                </a>

                <a class="menu-link {{ Request::is('siniestros/ausentes') ? 'active' : '' }}" href="{{ url('/siniestros/ausentes') }}">
                    <span class="menu-icon"><i class="fas fa-calendar-xmark"></i></span>
                    <span class="menu-text">Ausentes</span>
                </a>

                <a class="menu-link {{ Request::is('siniestros/terceros') ? 'active' : '' }}" href="{{ url('/siniestros/terceros') }}">
                    <span class="menu-icon"><i class="fas fa-business-time"></i></span>
                    <span class="menu-text">Terceros</span>
                </a>
            @endhasrole

            {{-- CEO --}}
            @hasrole('CEO')
                <div class="menu-section-title mt-3">Administración</div>

                <a class="menu-link {{ Request::is('usuarios') ? 'active' : '' }}" href="{{ url('/usuarios') }}">
                    <span class="menu-icon"><i class="fas fa-users"></i></span>
                    <span class="menu-text">Usuarios</span>
                </a>

                <a class="menu-link {{ Request::is('roles*') ? 'active' : '' }}" href="{{ url('/roles') }}">
                    <span class="menu-icon"><i class="fas fa-user-lock"></i></span>
                    <span class="menu-text">Roles</span>
                </a>
            @endhasrole

            {{-- Inspecciones --}}
            @hasrole('Inspecciones')
                <div class="menu-section-title mt-3">Inspecciones</div>

                <a class="menu-link {{ Request::is('siniestros/coordinados') ? 'active' : '' }}" href="{{ url('/siniestros/coordinados') }}">
                    <span class="menu-icon"><i class="fas fa-calendar-check"></i></span>
                    <span class="menu-text">Coordinados</span>
                </a>
            @endhasrole

            {{-- Inspectores --}}
            @hasrole('Inspectores')
                <div class="menu-section-title mt-3">Inspectores</div>

                <a class="menu-link {{ Request::is('siniestros/cotizaciones') ? 'active' : '' }}" href="{{ url('/siniestros/cotizaciones') }}">
                    <span class="menu-icon"><i class="fas fa-file-image"></i></span>
                    <span class="menu-text">Sólo cotizar</span>
                </a>

                <a class="menu-link {{ Request::is('siniestros/peritos') ? 'active' : '' }}" href="{{ url('/siniestros/peritos') }}">
                    <span class="menu-icon"><i class="fas fa-list-check"></i></span>
                    <span class="menu-text">IP asignadas</span>
                </a>

                <a class="menu-link {{ Request::is('siniestros/peritosgestion') ? 'active' : '' }}" href="{{ url('/siniestros/peritosgestion') }}">
                    <span class="menu-icon"><i class="fas fa-list-check"></i></span>
                    <span class="menu-text">IP en gestión</span>
                </a>
            @endhasrole

            {{-- Hojas de ruta --}}
            @hasrole('Hojas de ruta')
                <div class="menu-section-title mt-3">Hojas de ruta</div>

                <a class="menu-link {{ Request::is('siniestros/derivaciones') ? 'active' : '' }}" href="{{ url('/siniestros/derivaciones') }}">
                    <span class="menu-icon"><i class="fas fa-person-circle-check"></i></span>
                    <span class="menu-text">Asignar inspecciones</span>
                </a>
            @endhasrole

            <hr class="my-3">

            <div class="d-flex gap-2">
                <button type="button" class="btn btn-light border w-100 rounded-3" data-bs-dismiss="offcanvas">
                    Cerrar
                </button>
            </div>
        </div>
    </div>

    {{-- MAIN --}}
    <main class="app-main py-4">
        <div class="container">
            @yield('content')
        </div>
    </main>

</div>

@stack('scripts')
@yield('javas')
</body>
</html>
