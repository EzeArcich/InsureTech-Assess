<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f7faf8">
    <title>InsureTech Assess | Estudios de Siniestros</title>
    <style>
        :root { --green:#21823c; --green-dark:#14652c; --navy:#102b50; --muted:#536477; --line:#d7e1e8; }
        * { box-sizing:border-box; }
        body { margin:0; font-family:Inter,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif; color:var(--navy); background:#f8fafb; }
        .welcome { min-height:100vh; display:grid; grid-template-columns:minmax(0, 54%) minmax(0, 46%); overflow:hidden; }
        .content { display:flex; flex-direction:column; padding:clamp(24px,4vw,70px) clamp(24px,6vw,110px); position:relative; z-index:2; }
        .brand { display:flex; align-items:center; gap:12px; text-decoration:none; color:inherit; width:max-content; max-width:100%; }
        .brand-mark { width:52px; height:58px; flex:none; display:grid; place-items:center; background:linear-gradient(145deg,#56ad62,#19783b); color:#fff; clip-path:polygon(50% 0,94% 17%,90% 69%,50% 100%,10% 69%,6% 17%); }
        .brand-mark svg { width:29px; height:29px; }
        .brand-name { font-size:clamp(19px,2vw,26px); font-weight:800; letter-spacing:-.9px; line-height:1.1; }
        .brand-subtitle { font-size:13px; margin-top:4px; color:#4a5a6d; }
        .main { flex:1; display:flex; flex-direction:column; justify-content:center; width:100%; max-width:460px; padding:54px 0; }
        h1 { font-size:clamp(37px,4vw,57px); line-height:1.07; letter-spacing:-1.8px; margin:0 0 17px; }
        .intro { font-size:clamp(16px,1.3vw,19px); line-height:1.6; color:var(--muted); margin:0 0 35px; }
        .form-card { width:100%; }
        .field { margin-bottom:18px; }
        .field label { display:block; font-size:13px; font-weight:650; margin-bottom:8px; }
        .input-wrap { display:flex; align-items:center; border:1px solid var(--line); border-radius:9px; background:#fff; transition:border-color .2s,box-shadow .2s; }
        .input-wrap:focus-within { border-color:var(--green); box-shadow:0 0 0 3px rgba(33,130,60,.12); }
        .input-icon { width:19px; height:19px; margin-left:15px; color:#718093; flex:none; }
        .input-wrap input { width:100%; min-width:0; border:0; background:transparent; padding:14px 12px; font:inherit; font-size:15px; outline:0; color:var(--navy); }
        .input-wrap input::placeholder { color:#8996a5; }
        .eye { border:0; background:transparent; cursor:pointer; color:#65768b; padding:11px 14px; display:grid; place-items:center; }
        .eye svg { width:19px; height:19px; }
        .options { display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin:4px 0 24px; font-size:13px; }
        .remember { display:flex; align-items:center; gap:8px; color:var(--muted); cursor:pointer; }
        .remember input { accent-color:var(--green); width:16px; height:16px; }
        a.inline-link { color:var(--green-dark); font-weight:600; text-decoration:none; }
        a.inline-link:hover { text-decoration:underline; }
        .submit { display:flex; align-items:center; justify-content:center; gap:10px; width:100%; border:0; border-radius:9px; padding:15px 20px; font:inherit; font-weight:700; font-size:15px; color:#fff; background:linear-gradient(100deg,#238e41,#197735); cursor:pointer; box-shadow:0 8px 22px rgba(33,130,60,.15); transition:transform .15s,filter .15s; }
        .submit:hover { filter:brightness(1.07); transform:translateY(-1px); }
        .submit svg { width:18px; height:18px; }
        .switch { margin:25px 0 0; font-size:14px; color:var(--muted); text-align:center; }
        .alert { border:1px solid #f2b9b9; background:#fff0f0; color:#962c2c; padding:12px 15px; border-radius:9px; margin-bottom:22px; font-size:14px; }
        .alert ul { margin:6px 0 0; padding-left:19px; }
        .invalid { font-size:12px; color:#b42318; margin-top:6px; }
        .footer { color:#8995a2; font-size:12px; }
        .visual { position:relative; min-height:100vh; isolation:isolate; background:#243e4c; }
        .visual::before { content:""; position:absolute; inset:0; background:url('{{ asset('portada-welcome.webp') }}') 45% center/cover no-repeat; z-index:-2; }
        .visual::after { content:""; position:absolute; inset:0; background:linear-gradient(90deg,rgba(16,43,80,.28),rgba(16,43,80,.06)); z-index:-1; }
        .visual-curve { position:absolute; top:-12%; bottom:-12%; left:-120px; width:230px; background:#f8fafb; border-radius:0 52% 52% 0 / 0 50% 50% 0; transform:rotate(7deg); box-shadow:16px 0 0 rgba(41,148,68,.88); }
        .feature-stack { position:absolute; left:clamp(95px,17%,220px); top:50%; transform:translateY(-50%); display:flex; flex-direction:column; gap:25px; }
        .feature { display:flex; align-items:center; gap:14px; color:white; text-shadow:0 2px 8px rgba(0,0,0,.45); font-weight:700; font-size:16px; }
        .feature-icon { width:62px; height:62px; flex:none; display:grid; place-items:center; border:2px solid #269344; background:rgba(255,255,255,.96); color:#24853e; border-radius:50%; box-shadow:0 8px 28px rgba(0,0,0,.18); }
        .feature-icon svg { width:29px; height:29px; }
        .feature span { background:rgba(5,37,25,.42); padding:7px 12px; border-radius:7px; backdrop-filter:blur(3px); }
        [hidden] { display:none !important; }
        @media(max-width:900px) { .welcome { grid-template-columns:minmax(0,1fr) minmax(0,.65fr); } .content { padding:32px; } .visual-curve { left:-105px; } .feature-stack { left:90px; } .feature span { display:none; } }
        @media(max-width:650px) { .welcome { display:block; } .content { min-height:100vh; padding:28px 24px; } .main { margin:auto; padding:55px 0; } .visual { display:none; } .brand-name { font-size:21px; } h1 { font-size:42px; } }
    </style>
    @vite('resources/sass/surfaces.scss')
</head>
<body>
@php
    $canLogin = Route::has('login');
    $canRegister = Route::has('register');
    $canResetPassword = Route::has('password.request');
    $showRegister = $canRegister && old('_auth_mode') === 'register';
@endphp
<div class="welcome">
    <section class="content">
        <a class="brand" href="{{ url('/') }}" aria-label="InsureTech Assess - Inicio">
            <div class="brand-mark">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 11 7 6h10l2 5M4 11h16v7H4zM6 18v2m12-2v2M7 14h.01M17 14h.01"/></svg>
            </div>
            <div><div class="brand-name">InsureTech Assess</div><div class="brand-subtitle">Estudios de Siniestros</div></div>
        </a>

        <main class="main">
            @auth
                <h1>Bienvenido</h1>
                <p class="intro">Continuá gestionando tus estudios de siniestros junto a tu equipo.</p>
                <a class="submit" href="{{ route('home') }}" style="text-decoration:none">Ir al panel <span aria-hidden="true">→</span></a>
            @else
                <h1 id="welcome-title">{{ $showRegister ? 'Crear cuenta' : 'Bienvenido' }}</h1>
                <p class="intro" id="welcome-intro">{{ $showRegister ? 'Completá tus datos para comenzar a trabajar con tu equipo.' : 'Estudio, seguimiento y cierre de siniestros en un entorno colaborativo.' }}</p>

                @if ($errors->any())
                    <div class="alert" role="alert"><strong>Revisá los datos ingresados:</strong><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
                @endif

                @if ($canLogin)
                    <form id="login-form" class="form-card" method="POST" action="{{ route('login') }}" {{ $showRegister ? 'hidden' : '' }}>
                        @csrf
                        <input type="hidden" name="_auth_mode" value="login">
                        <div class="field"><label for="email">Correo electrónico</label><div class="input-wrap">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>
                            <input id="email" type="email" name="email" value="{{ old('email', Cookie::get('email')) }}" placeholder="nombre@estudio.com" autocomplete="username" required autofocus>
                        </div></div>
                        <div class="field"><label for="password">Contraseña</label><div class="input-wrap">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="5" y="10" width="14" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg>
                            <input id="password" type="password" name="password" placeholder="Ingresá tu contraseña" autocomplete="current-password" required>
                            <button class="eye" type="button" data-toggle-password="password" aria-label="Mostrar contraseña" aria-pressed="false"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg></button>
                        </div></div>
                        <div class="options"><label class="remember"><input type="checkbox" name="remember" value="1" {{ old('remember', Cookie::get('remember')) ? 'checked' : '' }}> Recordarme</label>
                            @if ($canResetPassword)<a class="inline-link" href="{{ route('password.request') }}">¿Olvidaste tu contraseña?</a>@endif
                        </div>
                        <button class="submit" type="submit">Ingresar <span aria-hidden="true">→</span></button>
                    </form>
                @endif

                @if ($canRegister)
                    <form id="register-form" class="form-card" method="POST" action="{{ route('register') }}" {{ $showRegister ? '' : 'hidden' }}>
                        @csrf
                        <input type="hidden" name="_auth_mode" value="register">
                        <div class="field"><label for="register-name">Nombre</label><div class="input-wrap"><input id="register-name" name="name" type="text" value="{{ old('name') }}" placeholder="Tu nombre completo" autocomplete="name" required></div></div>
                        <div class="field"><label for="register-email">Correo electrónico</label><div class="input-wrap"><input id="register-email" name="email" type="email" value="{{ old('email') }}" placeholder="nombre@estudio.com" autocomplete="email" required></div></div>
                        <div class="field"><label for="register-password">Contraseña</label><div class="input-wrap"><input id="register-password" name="password" type="password" placeholder="Creá una contraseña" autocomplete="new-password" required><button class="eye" type="button" data-toggle-password="register-password" aria-label="Mostrar contraseña" aria-pressed="false">◉</button></div></div>
                        <div class="field"><label for="register-password-confirmation">Confirmar contraseña</label><div class="input-wrap"><input id="register-password-confirmation" name="password_confirmation" type="password" placeholder="Repetí la contraseña" autocomplete="new-password" required></div></div>
                        <button class="submit" type="submit">Crear cuenta <span aria-hidden="true">→</span></button>
                    </form>
                    @if ($canLogin)<p class="switch" id="auth-switch"><span id="switch-text">{{ $showRegister ? '¿Ya tenés una cuenta?' : '¿Todavía no tenés cuenta?' }}</span> <a class="inline-link" href="#" id="switch-auth">{{ $showRegister ? 'Ingresar' : 'Registrarse' }}</a></p>@endif
                @endif
            @endauth
        </main>
        <footer class="footer">&copy; {{ date('Y') }} InsureTech Assess. Todos los derechos reservados.</footer>
    </section>
    <aside class="visual" aria-label="Estudio de siniestros y trabajo colaborativo">
        <div class="visual-curve" aria-hidden="true"></div>
        <div class="feature-stack">
            <div class="feature"><div class="feature-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 11 7 6h10l2 5M4 11h16v7H4zM6 18v2m12-2v2M7 14h.01M17 14h.01"/></svg></div><span>Estudio de siniestros</span></div>
            <div class="feature"><div class="feature-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><circle cx="9" cy="8" r="3"/><circle cx="17" cy="9" r="2"/><path d="M3 20v-2a6 6 0 0 1 12 0v2M15 15a5 5 0 0 1 6 5"/></svg></div><span>Trabajo en equipo</span></div>
            <div class="feature"><div class="feature-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M7 3h8l4 4v14H5V3h2Zm8 0v5h4M8 12h8M8 16h8"/></svg></div><span>Gestión de expedientes</span></div>
            <div class="feature"><div class="feature-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M4 20V11M10 20V5M16 20v-8M22 20V3M2 20h21"/></svg></div><span>Informes y resultados</span></div>
        </div>
    </aside>
</div>
<script>
    document.querySelectorAll('[data-toggle-password]').forEach(function (button) {
        button.addEventListener('click', function () {
            var input = document.getElementById(button.getAttribute('data-toggle-password'));
            var visible = input.type === 'password';
            input.type = visible ? 'text' : 'password';
            button.setAttribute('aria-pressed', String(visible));
            button.setAttribute('aria-label', visible ? 'Ocultar contraseña' : 'Mostrar contraseña');
        });
    });
    var switchAuth = document.getElementById('switch-auth');
    if (switchAuth) {
        switchAuth.addEventListener('click', function (event) {
            event.preventDefault();
            var login = document.getElementById('login-form');
            var register = document.getElementById('register-form');
            var registering = register.hidden;
            register.hidden = !registering;
            login.hidden = registering;
            document.getElementById('welcome-title').textContent = registering ? 'Crear cuenta' : 'Bienvenido';
            document.getElementById('welcome-intro').textContent = registering ? 'Completá tus datos para comenzar a trabajar con tu equipo.' : 'Estudio, seguimiento y cierre de siniestros en un entorno colaborativo.';
            document.getElementById('switch-text').textContent = registering ? '¿Ya tenés una cuenta?' : '¿Todavía no tenés cuenta?';
            switchAuth.textContent = registering ? 'Ingresar' : 'Registrarse';
        });
    }
</script>
</body>
</html>
