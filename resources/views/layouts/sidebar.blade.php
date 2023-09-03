<style>
    .text-center {
        font-family: 'Bebas Neue', cursive;
        background: linear-gradient(to right, red, orange);
        -webkit-background-clip: text; /* Para navegadores Webkit (Chrome, Safari, etc.) */
        background-clip: text;
        color: transparent; /* Oculta el color del texto */
    }
</style>

<aside id="sidebar-wrapper">
    
    <div class="sidebar-brand">
    <h5 class="text-center pt-3">InsureTech Assess</h5>
        <a href="{{ url('/home') }}"></a>
    </div>
    
    <div class="sidebar-brand sidebar-brand-sm">
        <a href="{{ url('/home') }}" class="small-sidebar-text">
            <img class="navbar-brand-full" src="{{ asset('img/DAG.png') }}" width="45px" alt=""/>
        </a>
    </div>
    <ul class="sidebar-menu">
        @include('layouts.menu')
    </ul>
</aside>
