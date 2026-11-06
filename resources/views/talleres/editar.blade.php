@extends('layouts.app')
@section('page-title', 'Talleres')

@section('content')
<section class="py-2">

    {{-- Header --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h3 class="mb-0 fw-bold">Editar taller</h3>
            <nav aria-label="breadcrumb" class="mt-1">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('workshops.index') }}" class="text-decoration-none">Talleres</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Editar</li>
                </ol>
            </nav>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('workshops.index') }}" class="btn btn-light border btn-sm rounded-3">
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

    <form action="{{ route('workshops.update', $talleres->id) }}" method="POST" enctype="multipart/form-data">
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
                    <label for="name" class="form-label">Nombre del taller</label>
                    <input type="text" name="name" id="name" class="form-control"
                           value="{{ old('name', $talleres->name) }}">
                </div>

                <div class="col-12 col-md-4">
                    <label for="business_name" class="form-label">Razón social</label>
                    <input type="text" name="business_name" id="business_name" class="form-control"
                           value="{{ old('business_name', $talleres->business_name) }}">
                </div>

                <div class="col-12 col-md-4">
                    <label for="tax_id" class="form-label">CUIT</label>
                    <input type="text" name="tax_id" id="tax_id" class="form-control"
                           value="{{ old('tax_id', $talleres->tax_id) }}">
                </div>

                <div class="col-12 col-md-6">
                    <label for="address" class="form-label">Dirección</label>
                    <input type="text" name="address" id="address" class="form-control"
                           value="{{ old('address', $talleres->address) }}">
                </div>

                <div class="col-12 col-md-3">
                    <label for="neighborhood" class="form-label">Barrio</label>
                    <input type="text" name="neighborhood" id="neighborhood" class="form-control"
                           value="{{ old('neighborhood', $talleres->neighborhood) }}">
                </div>

                <div class="col-12 col-md-3">
                    <label for="city" class="form-label">Localidad</label>
                    <input type="text" name="city" id="city" class="form-control"
                           value="{{ old('city', $talleres->city) }}">
                </div>

                <div class="col-12">
                    <label for="phone" class="form-label">Teléfonos</label>
                    <input type="text" name="phone" id="phone" class="form-control"
                           value="{{ old('phone', $talleres->phone) }}">
                </div>

                <div class="col-12 col-md-6">
                    <label for="email" class="form-label">E-mail</label>
                    <input type="text" name="email" id="email" class="form-control" 
                           value="{{ old('email', $talleres->email) }}">
                </div>

                <div class="col-12 col-md-6">
                    <label for="area" class="form-label">Zona</label>
                    <input type="text" name="area" id="area" class="form-control"
                           value="{{ old('area', $talleres->area) }}">
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
                    <label for="towing" class="form-label">Grúa</label>
                    <input type="text" name="towing" id="towing" class="form-control"
                           value="{{ old('towing', $talleres->towing) }}">
                </div>

                <div class="col-12 col-md-4">
                    <label for="transport" class="form-label">Traslado</label>
                    <input type="text" name="transport" id="transport" class="form-control"
                           value="{{ old('transport', $talleres->transport) }}">
                </div>

                <div class="col-12 col-md-4">
                    <label for="hail" class="form-label">Granizo</label>
                    <input type="text" name="hail" id="hail" class="form-control"
                           value="{{ old('hail', $talleres->hail) }}">
                </div>

                <div class="col-12 col-md-4">
                    <label for="repair_availability" class="form-label">Pone repuestos</label>
                    <input type="text" name="repair_availability" id="repair_availability" class="form-control"
                           value="{{ old('repair_availability', $talleres->repair_availability) }}">
                </div>

                <div class="col-12 col-md-4">
                    <label for="holidays" class="form-label">Vacaciones</label>
                    <input type="text" name="holidays" id="holidays" class="form-control"
                           value="{{ old('holidays', $talleres->holidays) }}">
                </div>

                <div class="col-12 col-md-4">
                    <label for="deductible_discount" class="form-label">Descuento de franquicia</label>
                    <input type="text" name="deductible_discount" id="deductible_discount" class="form-control"
                           value="{{ old('deductible_discount', $talleres->deductible_discount) }}">
                </div>

            </div>
        </div>

        {{-- Actions --}}
        <div class="d-flex flex-wrap gap-2">
            <button type="submit" class="btn btn-success px-4">
                <i class="fas fa-check me-2"></i>
                Aplicar cambios
            </button>

            <a href="{{ route('workshops.index') }}" class="btn btn-outline-secondary px-4">
                <i class="fas fa-times me-2"></i>
                Cancelar
            </a>
        </div>

    <div class="mb-3"><input type="hidden" name="is_approved" value="0"><label><input type="checkbox" name="is_approved" value="1" @checked(old('is_approved', $talleres->is_approved))> Taller homologado</label></div><div class="mb-3"><label class="form-label">Horarios</label><textarea name="opening_hours" class="form-control">{{ old('opening_hours', $talleres->opening_hours) }}</textarea></div></form>
</section>
@endsection
