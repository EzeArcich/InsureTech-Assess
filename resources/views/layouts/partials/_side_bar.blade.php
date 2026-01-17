    {{-- OFFCANVAS MENU --}}
    <div class="offcanvas offcanvas-start app-offcanvas"
         tabindex="-1"
         id="appOffcanvas"
         aria-labelledby="appOffcanvasLabel">
        <div class="offcanvas-header border-bottom bg-primary">
            <div>
                <h5 class="offcanvas-title mb-0 fw-bold text-white" id="appOffcanvasLabel">Navegación</h5>
                <small class="d-block mt-1 text-white">Accesos rápidos</small>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>

        <div class="offcanvas-body bg-info">

            {{-- Sección general --}}
            <div class="menu-section-title">General</div>

            <a class="menu-link {{ Request::is('home') ? 'active' : '' }}" href="{{ url('/home') }}">
                <span class="menu-icon"><i class="bi bi-speedometer"></i></span>
                <span class="menu-text">Dashboard</span>
            </a>

            <a class="menu-link {{ Request::is('talleres*') ? 'active' : '' }}" href="{{ url('/talleres') }}">
                <span class="menu-icon"><i class="bi bi-tools"></i></span>
                <span class="menu-text">Talleres homologados</span>
            </a>

            <a class="menu-link {{ Request::is('siniestros') && !Request::is('siniestros/*') ? 'active' : '' }}" href="{{ url('/siniestros') }}">
                <span class="menu-icon"><i class="bi bi-exclamation-circle"></i></i></span>
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

            <hr class="my-4" style="border-color: rgba(15, 23, 42, .08);">

            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-success w-100 rounded-3" data-bs-dismiss="offcanvas">
                    <i class="fas fa-times me-2"></i>
                    Cerrar menú
                </button>
            </div>
        </div>
    </div>