@extends('layouts.app')

@section('content')
<section class="py-2">

    {{-- Header --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h3 class="mb-0 fw-bold">Editar taller</h3>
            <nav aria-label="breadcrumb" class="mt-1">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('talleres.index') }}" class="text-decoration-none">Talleres</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Editar</li>
                </ol>
            </nav>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('talleres.index') }}" class="btn btn-light border btn-sm rounded-3">
                <i class="fas fa-arrow-left me-2"></i> Volver
            </a>
        </div>
    </div>

    {{-- Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger border-0 shadow-sm">
            <div class="fw-bold mb-2">Revisá estos campos:</div>
            <div class="d-flex flex-wrap gap-2">
                @foreach ($errors->all() as $error)
                    <span class="badge text-bg-danger">{{ $error }}</span>
                @endforeach
            </div>
        </div>
    @endif

    <form action="{{ route('talleres.update', $talleres->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Panel 1: Datos del taller --}}
        <div class="panel mb-3">
            <div class="d-flex align-items-center justify-content-between mb-3 panel-header-deep">
                <div>
                    <h5 class="mb-0 fw-bold">Datos del taller</h5>
                    <small class="text-muted">Información principal del taller</small>
                </div>
                <span class="badge text-bg-light border">Alta</span>
            </div>

            <div class="row g-3">

                <div class="col-12 col-md-4">
                    <label for="taller" class="form-label">Nombre del taller</label>
                    <input type="text" name="taller" id="taller" class="form-control" 
                           value="{{ old('taller', $talleres->taller) }}">
                </div>

                <div class="col-12 col-md-4">
                    <label for="razon" class="form-label">Razón social</label>
                    <input type="text" name="razon" id="razon" class="form-control" 
                           value="{{ old('razon', $talleres->razon) }}">
                </div>

                <div class="col-12 col-md-4">
                    <label for="cuit" class="form-label">CUIT</label>
                    <input type="text" name="cuit" id="cuit" class="form-control" 
                           value="{{ old('cuit', $talleres->cuit) }}">
                </div>

                <div class="col-12 col-md-6">
                    <label for="direccion" class="form-label">Dirección</label>
                    <input type="text" name="direccion" id="direccion" class="form-control" 
                           value="{{ old('direccion', $talleres->direccion) }}">
                </div>

                <div class="col-12 col-md-3">
                    <label for="barrio" class="form-label">Barrio</label>
                    <input type="text" name="barrio" id="barrio" class="form-control" 
                           value="{{ old('barrio', $talleres->barrio) }}">
                </div>

                <div class="col-12 col-md-3">
                    <label for="localidad" class="form-label">Localidad</label>
                    <input type="text" name="localidad" id="localidad" class="form-control" 
                           value="{{ old('localidad', $talleres->localidad) }}">
                </div>

                <div class="col-12">
                    <label for="telefonos" class="form-label">Teléfonos</label>
                    <input type="text" name="telefonos" id="telefonos" class="form-control" 
                           value="{{ old('telefonos', $talleres->telefonos) }}">
                </div>

                <div class="col-12 col-md-6">
                    <label for="email" class="form-label">E-mail</label>
                    <input type="text" name="email" id="email" class="form-control" 
                           value="{{ old('email', $talleres->email) }}">
                </div>

                <div class="col-12 col-md-6">
                    <label for="zona" class="form-label">Zona</label>
                    <input type="text" name="zona" id="zona" class="form-control" 
                           value="{{ old('zona', $talleres->zona) }}">
                </div>

            </div>
        </div>

        {{-- Panel 2: Servicios y características --}}
        <div class="panel mb-3">
            <div class="d-flex align-items-center justify-content-between mb-3 panel-header-sage">
                <div>
                    <h5 class="mb-0 fw-bold">Servicios y características</h5>
                    <small class="text-muted">Servicios que ofrece el taller</small>
                </div>
                <span class="badge text-bg-light border">Servicios</span>
            </div>

            <div class="row g-3">

                <div class="col-12 col-md-4">
                    <label for="grua" class="form-label">Grúa</label>
                    <input type="text" name="grua" id="grua" class="form-control" 
                           value="{{ old('grua', $talleres->grua) }}">
                </div>

                <div class="col-12 col-md-4">
                    <label for="traslado" class="form-label">Traslado</label>
                    <input type="text" name="traslado" id="traslado" class="form-control" 
                           value="{{ old('traslado', $talleres->traslado) }}">
                </div>

                <div class="col-12 col-md-4">
                    <label for="granizo" class="form-label">Granizo</label>
                    <input type="text" name="granizo" id="granizo" class="form-control" 
                           value="{{ old('granizo', $talleres->granizo) }}">
                </div>

                <div class="col-12 col-md-4">
                    <label for="ponerep" class="form-label">Pone repuestos</label>
                    <input type="text" name="ponerep" id="ponerep" class="form-control" 
                           value="{{ old('ponerep', $talleres->ponerep) }}">
                </div>

                <div class="col-12 col-md-4">
                    <label for="vacaciones" class="form-label">Vacaciones</label>
                    <input type="text" name="vacaciones" id="vacaciones" class="form-control" 
                           value="{{ old('vacaciones', $talleres->vacaciones) }}">
                </div>

                <div class="col-12 col-md-4">
                    <label for="descfranq" class="form-label">Descuento de franquicia</label>
                    <input type="text" name="descfranq" id="descfranq" class="form-control" 
                           value="{{ old('descfranq', $talleres->descfranq) }}">
                </div>

            </div>
        </div>

        {{-- Actions --}}
        <div class="d-flex flex-wrap gap-2">
            <button type="submit" class="btn btn-success px-4">
                <i class="fas fa-check me-2"></i>
                Aplicar cambios
            </button>

            <a href="{{ route('talleres.index') }}" class="btn btn-outline-secondary px-4">
                <i class="fas fa-times me-2"></i>
                Cancelar
            </a>
        </div>

    </form>
</section>
@endsection
