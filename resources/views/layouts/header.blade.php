<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<style>
    .nav-link-user:hover {
    cursor: pointer;
}

</style>

<form class="form-inline mr-auto" action="#">
    <ul class="navbar-nav mr-3">
        <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg text-dark"><i class="fas fa-bars"></i></a></li>
    </ul>
</form>
<ul class="navbar-nav navbar-right ms-auto">
    @if(\Illuminate\Support\Facades\Auth::user())
        <li class="nav-item dropdown">
            <a href="#" class="nav-link dropdown-toggle nav-link-lg nav-link-user" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <img alt="image" src="{{ asset('img/logo.png') }}" class="rounded-circle mr-1 thumbnail-rounded user-thumbnail pb-2">
                <div class="d-sm-none d-lg-inline-block">
                    <h5 class="text-dark">{{ \Illuminate\Support\Facades\Auth::user()->name }}</h5>
                </div>
            </a>

            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                <div class="dropdown-title">Panel</div>
                <a class="dropdown-item has-icon edit-profile d-flex align-items-center" href="{{ route('usuarios.edit', ['usuario' => \Auth::id()]) }}">
                    <i class="fa fa-user"></i><h5 class="mb-0 ms-2">Editar perfil</h5>
                </a>
                <a class="dropdown-item has-icon d-flex align-items-center" data-toggle="modal" style="cursor:pointer;" data-target="#changePasswordModal" data-id="{{ \Auth::id() }}">
                    <i class="fa fa-lock"></i><h5 class="mb-0 ms-2">Cambiar clave</h5>
                </a>
                <a href="{{ url('logout') }}" class="dropdown-item has-icon text-danger d-flex align-items-center" onclick="event.preventDefault(); localStorage.clear(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i><h5 class="mb-0 ms-2">Salir</h5>
                </a>

                <form id="logout-form" action="{{ url('/logout') }}" method="POST" class="d-none">
                    {{ csrf_field() }}
                </form>
            </div>

        </li>
    @else
        <li class="nav-item dropdown">
            <a href="#" class="nav-link dropdown-toggle nav-link-lg nav-link-user" data-bs-toggle="dropdown" aria-expanded="false">
                <div class="d-sm-none d-lg-inline-block">{{ __('messages.common.hello') }}</div>
            </a>
            <div class="dropdown-menu dropdown-menu-end">
                <div class="dropdown-title">{{ __('messages.common.login') }}
                    / {{ __('messages.common.register') }}</div>
                <a href="{{ route('login') }}" class="dropdown-item has-icon">
                    <i class="fas fa-sign-in-alt"></i> {{ __('messages.common.login') }}
                </a>
                <div class="dropdown-divider"></div>
                <a href="{{ route('register') }}" class="dropdown-item has-icon">
                    <i class="fas fa-user-plus"></i> {{ __('messages.common.register') }}
                </a>
            </div>
        </li>
    @endif
</ul>
