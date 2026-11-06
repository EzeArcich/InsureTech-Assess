    <aside class="claims-sidebar" aria-label="Navegación principal">
        <a href="{{ route('home') }}" class="claims-brand">
            <span class="claims-brand-shield"><i class="bi bi-shield-check"></i></span>
            <span>InsureTech<small>Assess</small></span>
        </a>
        <nav class="claims-navigation">
            <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'is-active' : '' }}"><i class="bi bi-house-door"></i> Dashboard</a>
            @php
                $claimsSection = request()->routeIs('claims.*', 'clients.*', 'inspectors.*');
                $claimsListRoute = auth()->user()->can('view-all-claims') ? 'claims.all' : 'claims.index';
                $stateLinks = [
                    'Pendiente' => ['Pendientes', 'orange', 'claims.pending'],
                    'Ausente' => ['Ausentes', 'gray', 'claims.absent'],
                    'Coordinado' => ['Coordinados', 'yellow', 'claims.coordinated'],
                    'Peritando' => ['Peritando', 'blue', 'claims.assigned'],
                    'IPgestion' => ['En gestión', 'green', 'claims.in_progress'],
                ];
                if (auth()->user()->isRoutePlanner()) {
                    $stateLinks = array_intersect_key($stateLinks, array_flip(['Coordinado', 'Peritando']));
                }
                if (auth()->user()->isRestrictedInspector()) {
                    $stateLinks = array_intersect_key($stateLinks, array_flip(['Peritando', 'IPgestion']));
                    $stateLinks['IPgestion'][0] = 'En gestión';
                }
            @endphp
            <details class="nav-claims {{ $claimsSection ? 'is-active' : '' }}" @if($claimsSection) open @endif>
                <summary aria-label="Desplegar filtros de siniestros"><i class="bi bi-car-front-fill"></i><span>Siniestros</span><i class="bi bi-chevron-down nav-claims-chevron"></i></summary>
                <div class="nav-claims-links">
                    <a href="{{ route($claimsListRoute) }}" class="{{ request()->routeIs($claimsListRoute) && !request()->filled('status') && !request()->filled('company_id') ? 'is-active' : '' }}">
                        <i class="bi bi-list-ul"></i> {{ auth()->user()->isRestrictedInspector() ? 'Mis inspecciones' : ($claimsListRoute === 'claims.all' ? 'Todos los siniestros' : 'Listado general') }}
                    </a>
                    @foreach($stateLinks as $state => [$label, $color, $stateRoute])
                        @php($isCurrentState = $claimsSection && (request('status') === $state || request()->routeIs($stateRoute)))
                        <a href="{{ route($claimsListRoute, ['status' => $state]) }}" class="{{ $isCurrentState ? 'is-active' : '' }}" @if($isCurrentState) aria-current="page" @endif>
                            <span class="nav-state-dot nav-state-dot--{{ $color }}" aria-hidden="true"></span>{{ $label }}
                        </a>
                    @endforeach
                    @if(auth()->user()->isRestrictedInspector())
                        <a href="{{ route('claims.index', ['status' => 'Peritando', 'date' => now()->toDateString()]) }}"><i class="bi bi-calendar-check"></i>Inspecciones de hoy</a>
                    @endif
                </div>
            </details>
            @unless(auth()->user()->isRestrictedInspector() || auth()->user()->isRoutePlanner())
            <a href="{{ route('workshops.index') }}" class="{{ request()->routeIs('workshops.*') ? 'is-active' : '' }}"><i class="bi bi-building-check"></i> Talleres</a>
            <a href="{{ route('blogs.index') }}" class="{{ request()->routeIs('blogs.*') ? 'is-active' : '' }}"><i class="bi bi-journal-text"></i> Noticias</a>
            <a href="{{ route('roles.index') }}" class="{{ request()->routeIs('roles.*') ? 'is-active' : '' }}"><i class="bi bi-gear"></i> Roles</a>
            @endunless
            @if((!auth()->user()->isRestrictedInspector() && !auth()->user()->isRoutePlanner()) || app()->environment('local'))
            <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'is-active' : '' }}"><i class="bi bi-people"></i> Usuarios</a>
            @endif
            @hasrole('CEO')
            <details class="nav-claims {{ request()->routeIs('settings.*') ? 'is-active' : '' }}" @if(request()->routeIs('settings.*')) open @endif>
                <summary><i class="bi bi-gear"></i><span>Configuración</span><i class="bi bi-chevron-down nav-claims-chevron"></i></summary>
                <div class="nav-claims-links"><a href="{{ route('settings.repair-rates') }}" class="{{ request()->routeIs('settings.repair-rates*') ? 'is-active' : '' }}">Chapa, pintura y mecánica</a><a href="{{ route('settings.inspection-rates') }}" class="{{ request()->routeIs('settings.inspection-rates*') ? 'is-active' : '' }}">Inspecciones</a></div>
            </details>
            @endhasrole
            @if(auth()->user()->canPlanInspectionRoutes() || auth()->user()->isRestrictedInspector())
            <a href="{{ route('route-sheets.index') }}" class="{{ request()->routeIs('route-sheets.*') ? 'is-active' : '' }}"><i class="bi bi-map"></i> Hoja de ruta</a>
            @endif
            @if(auth()->user()->canManageInspectionPayments() || auth()->user()->isRestrictedInspector())
            <a href="{{ route('inspection-payments.index') }}" class="{{ request()->routeIs('inspection-payments.*') ? 'is-active' : '' }}"><i class="bi bi-cash-stack"></i> {{ auth()->user()->canManageInspectionPayments() ? 'Honorarios' : 'Mis honorarios' }}</a>
            @endif
        </nav>
        <div class="sidebar-account">
            <hr class="sidebar-account-divider">
            <div class="sidebar-user">
                <div class="claims-avatar" aria-hidden="true">{{ collect(explode(' ', auth()->user()->name))->filter()->take(2)->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))->implode('') }}</div>
                <span class="sidebar-user-name" title="{{ auth()->user()->name }}">{{ auth()->user()->name }}</span>
            </div>
            <form action="{{ route('logout') }}" method="POST" class="sidebar-logout-form">
                @csrf
                <button class="sidebar-logout" type="submit" title="Cerrar sesión"><i class="bi bi-box-arrow-right" aria-hidden="true"></i><span>Cerrar sesión</span></button>
            </form>
        </div>
    </aside>
