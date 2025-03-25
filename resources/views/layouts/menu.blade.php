<li class="side-menus">

    <a class="nav-link {{ Request::is('home') ? 'active' : '' }}" href="/home">
        <i class="fas fa-table fa-xl"></i><span style="font-size:16px">. Dashboard</span>
    </a>
    
    @hasrole('Coordinaciones')        
        <a class="nav-link {{ Request::is('blogs') ? 'active' : '' }}" href="/blogs">
            <i class="fas fa-blog fa-xl"></i><span style="font-size:16px">. Noticias</span>
        </a>

        <a class="nav-link {{ Request::is('siniestros/pendientes') ? 'active' : '' }}" href="/siniestros/pendientes">
            <i class="fas fa-clock fa-xl"></i><span style="font-size:16px">. Pendientes</span>
        </a>

        <a class="nav-link {{ Request::is('siniestros/ausentes') ? 'active' : '' }}" href="/siniestros/ausentes">
            <i class="fas fa-calendar-xmark fa-xl"></i><span style="font-size:16px">. Ausentes</span>
        </a>
        
        <a class="nav-link {{ Request::is('siniestros/terceros') ? 'active' : '' }}" href="/siniestros/terceros">
            <i class="fas fa-business-time fa-xl"></i><span style="font-size:16px">. Terceros</span>
        </a>
    @endhasrole

    @hasrole('CEO')

        <a class="nav-link {{ Request::is('siniestros*') ? 'active' : '' }}" href="/siniestros">
            <i class="fas fa-car fa-xl"></i><span style="font-size:16px">. Siniestros</span>
        </a>

        <a class="nav-link {{ Request::is('usuarios') ? 'active' : '' }}" href="/usuarios">
            <i class="fas fa-users fa-xl"></i><span style="font-size:16px">. Usuarios</span>
        </a>

        <a class="nav-link {{ Request::is('roles*') ? 'active' : '' }}" href="/roles">
            <i class="fas fa-user-lock fa-xl"></i><span style="font-size:16px">. Roles</span>
        </a>
    @endhasrole
    
    @hasrole('Inspecciones')
        <a class="nav-link {{ Request::is('siniestros/coordinados') ? 'active' : '' }}" href="/siniestros/coordinados">
            <i class="fas fa-calendar-check fa-xl"></i><span style="font-size:16px">. Coordinados</span>
        </a>
    @endhasrole

    @hasrole('Inspectores')
        <a class="nav-link {{ Request::is('siniestros/cotizaciones') ? 'active' : '' }}" href="/siniestros/cotizaciones">
            <i class="fas fa-file-image fa-xl"></i><span style="font-size:16px">. Sólo cotizar</span>
        </a>
        
        <a class="nav-link {{ Request::is('siniestros/peritos') ? 'active' : '' }}" href="/siniestros/peritos">
            <i class="fas fa-list-check fa-xl"></i><span style="font-size:16px">. IP asignadas</span>
        </a>

        <a class="nav-link {{ Request::is('siniestros/peritosgestion') ? 'active' : '' }}" href="/siniestros/peritosgestion">
            <i class="fas fa-list-check fa-xl"></i><span style="font-size:16px">. IP en gestión</span>
        </a>
    @endhasrole

    @hasrole('Hojas de ruta')
        <a class="nav-link {{ Request::is('siniestros/derivaciones') ? 'active' : '' }}" href="/siniestros/derivaciones">
            <i class="fas fa-person-circle-check fa-xl"></i><span style="font-size:16px">. Asignar inspecciones</span>
        </a>
    @endhasrole

    <a class="nav-link {{ Request::is('talleres') ? 'active' : '' }}" href="/talleres">
        <i class="fas fa-wrench fa-xl"></i><span style="font-size:16px">. Talleres homologados</span>
    </a>

</li>
