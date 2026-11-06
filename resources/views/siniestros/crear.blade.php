@extends('layouts.app')

@push('styles')
<link href="https://unpkg.com/dropzone@6.0.0-beta.1/dist/dropzone.css" rel="stylesheet" />

<style>
    /* Selección de fila en tablas de modal */
    .row-selected {
        background: rgba(4,57,94,.10) !important;
        font-weight: 700;
    }
</style>
@endpush

@section('content')
<section class="py-2 app-elevated-section">

    {{-- Header --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 app-elevated-heading">
        <div>
            <h3 class="mb-0 fw-bold">Ingresar siniestro</h3>
            <nav aria-label="breadcrumb" class="mt-1">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('claims.index') }}" class="text-decoration-none">Siniestros</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Crear</li>
                </ol>
            </nav>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('claims.index') }}" class="btn btn-light border btn-sm rounded-3">
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

    <form class="app-elevated-form" action="{{ route('claims.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- Panel 1: Datos del siniestro --}}
        <div class="panel mb-3">
            <div class="d-flex align-items-center justify-content-between mb-3 panel-header-deep">
                <div>
                    <h5 class="mb-0 fw-bold">Datos del siniestro</h5>
                    <small class="text-muted">Carga la información base</small>
                </div>
                <span class="badge text-bg-light border">Alta</span>
            </div>

            <div class="row g-3">

<div class="col-12 col-md-3">
                    <label for="claim_number" class="form-label">Siniestro</label>
                    <input type="text" name="claim_number" id="claim_number" class="form-control"
                           value="{{ $siniestro->claim_number ?? '' }}">
                </div>

                <div class="col-12 col-md-3">
                    <label for="license_plate" class="form-label">Patente</label>
                    <input type="text" name="license_plate" id="license_plate" class="form-control"
                           value="{{ $siniestro->license_plate ?? '' }}">
                    <div id="error_patente" class="form-text"></div>
                </div>

                <div class="col-12 col-md-3">
                    <label for="company_id" class="form-label">Compañía</label>
                    <select name="company_id" id="company_id" class="form-select" required>
                        <option value="">Seleccionar compañía...</option>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}" @selected(old('company_id', $siniestro->company_id) == $company->id)>{{ $company->name }}</option>
                        @endforeach
                    </select>
                </div>

<div class="col-md-6">
                    <label for="client_name" class="form-label">Nombre del asegurado</label>
                    <input type="text" name="client_name" id="client_name" class="form-control" required
                           value="{{ old('client_name', $siniestro->client_name) }}">
                </div>
                <div class="col-md-6">
                    <label for="client_email" class="form-label">Email del asegurado</label>
                    <input type="email" name="client_email" id="client_email" class="form-control"
                           value="{{ old('client_email', $siniestro->client?->email) }}">
                </div>
                <div class="col-12 col-md-3">
                    <label for="client_type" class="form-label">Tipo de cliente</label>
                    <select class="form-select" id="client_type" name="client_type">
                        <option value="{{ $siniestro->client_type ?? '' }}" selected>
                            {{ $siniestro->client_type ?? 'Seleccionar...' }}
                        </option>
                        <option value="Asegurado">Asegurado</option>
                        <option value="Tercero">Tercero</option>
                        <option value="Cotizacion">Cotización</option>
                    </select>
                </div>

                <div class="col-12 col-md-3">
                    <label for="reason" class="form-label">Motivo</label>
                    <select class="form-select" id="reason" name="reason">
                        <option value="{{ $siniestro->reason ?? '' }}" selected>
                            {{ $siniestro->reason ?? 'Seleccionar...' }}
                        </option>
                        <option value="Todo riesgo">Todo riesgo</option>
                        <option value="ampliacion">Ampliación</option>
                        <option value="actualizaciondevalores">Actualización de valores</option>
                        <option value="cotizarsincerrar">Cotizar y devolver sin cerrar</option>
                        <option value="robo">Robo</option>
                        <option value="incendio">Incendio</option>
                        <option value="posibleDT">Posible DT</option>
                    </select>
                </div>

                {{-- Productor --}}
                <div class="col-12 col-md-6">
                    <label for="producer_name" class="form-label">Productor</label>
                    <div class="d-flex flex-wrap align-items-center gap-3">
                    <div class="input-group producer-picker">
                        <input type="hidden" name="producer_id" id="producer_id" value="{{ $siniestro->producer_id }}">
                        <input type="text" class="form-control name" id="producer_name" name="producer_name"
                               value="{{ $siniestro->producer_name ?? '' }}">
                        <button class="btn btn-outline-primary"
                                type="button"
                                data-bs-toggle="modal"
                                data-bs-target="#modal_productores">
                            <i class="fas fa-search-plus"></i>
                        </button>
                    </div>
                    <button class="btn btn-palette-gray" type="button" disabled title="Guardá el siniestro para enviar un correo al productor"><i class="bi bi-envelope"></i> Correo</button>
                    </div>
                </div>

</div>
        </div>

        {{-- Panel 2: Datos de coordinación --}}
        <div class="panel mb-3 DatCord">
            <div class="d-flex align-items-center justify-content-between mb-3 panel-header-sage">
                <div>
                    <h5 class="mb-0 fw-bold">Datos de coordinación</h5>
                    <small class="text-muted">Información para gestión y asignación</small>
                </div>
                <span class="badge text-bg-light border">Coordinación</span>
            </div>

            <div class="row g-3">

                <div class="col-12">
                    <label for="notes" class="form-label">Observaciones</label>
                    <textarea class="form-control" name="notes" id="notes" rows="3">{{ $siniestro->notes ?? '' }}</textarea>
                </div>

                {{-- Taller --}}
                <div class="col-12 col-md-6">
                    <label for="workshop_name" class="form-label">Nombre del taller</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="workshop_name" name="workshop_name"
                               value="{{ $siniestro->workshop_name ?? '' }}">
                        <button class="btn btn-outline-primary"
                                type="button"
                                data-bs-toggle="modal"
                                data-bs-target="#modal_talleres">
                            <i class="fas fa-search-plus"></i>
                        </button>
                    </div>
                </div>

                <div class="col-12 col-md-3">
                    <label for="email" class="form-label">E-mail</label>
                    <input type="text" name="email" class="form-control" id="email"
                           value="{{ $siniestro->email ?? '' }}">
                </div>

                <div class="col-12 col-md-3">
                    <label for="phone" class="form-label">Teléfono</label>
                    <input type="text" name="phone" class="form-control" id="phone"
                           value="{{ $siniestro->phone ?? '' }}">
                </div>

                <div class="col-12 col-md-6">
                    <label for="address" class="form-label">Dirección</label>
                    <input type="text" name="address" class="form-control" id="address"
                           value="{{ $siniestro->address ?? '' }}">
                </div>

                <div class="col-12 col-md-6">
                    <label for="city" class="form-label">Localidad</label>
                    <input type="text" name="city" class="form-control" id="city"
                           value="{{ $siniestro->city ?? '' }}">
                </div>

                {{-- Coordinador hidden --}}
                <input type="hidden" name="coordinator" id="coordinator" value="{{ auth()->user()->name }}">

                <div class="col-12 col-md-4">
                    <label for="inspection_mode" class="form-label">Tipo de inspección</label>
                    <select class="form-select" id="inspection_mode" name="inspection_mode">
                        <option value="{{ $siniestro->inspection_mode ?? '' }}" selected>
                            {{ $siniestro->inspection_mode ?? 'Seleccionar...' }}
                        </option>
                        <option value="presencial">Presencial</option>
                        <option value="videollamada">Videollamada</option>
                        <option value="foto y presupuesto">Por foto y presupuesto</option>
                        <option value="foto">Por foto</option>
                    </select>
                </div>

                <div class="col-12 col-md-4">
                    <label for="status" class="form-label">Estado</label>
                    <select class="form-select" id="status" name="status">
                        <option value="Pendiente" selected>Pendiente</option>
                        <option value="Coordinado">Coordinado</option>
                        <option value="Ausente">Ausente</option>
                        @can('derivar-siniestro')
                            <option value="Peritando">Derivado a inspector</option>
                        @endcan
                        <option value="Reclamo de repuestos">Reclamo de repuestos</option>
                        <option value="Actualizacion de valores">Actualización de valores</option>
                        <option value="Cargar ampliacion">Cargar ampliación</option>
                        <option value="Baja">Baja</option>
                    </select>
                </div>

                <div class="col-12 col-md-4">
                    <label for="inspection_date" class="form-label">Fecha IP</label>
                    <input type="date" name="inspection_date" class="form-control" id="inspection_date"
                           value="{{ $siniestro->inspection_date?->format('Y-m-d') ?? '' }}">
                </div>

                <div class="col-12 col-md-4">
                    <label for="send_order" class="form-label">Enviar orden</label>
                    <select class="form-select" id="send_order" name="send_order">
                        <option value="si" selected>Si</option>
                        <option value="no">No</option>
                    </select>
                </div>

                <div class="col-12 col-md-4">
                    <label for="inspection_time" class="form-label">Rango horario</label>
                    <input type="text" name="inspection_time" class="form-control" id="inspection_time"
                           value="{{ $siniestro->inspection_time ?? '' }}">
                </div>

                <div class="col-12 col-md-4">
                    <label for="image" class="form-label">Cover</label>
                    <input type="file" name="image" class="form-control">
                </div>

                <div class="col-12">
                    <label for="inspector_notes" class="form-label">Comentarios para el perito</label>
                    <textarea class="form-control" name="inspector_notes" id="inspector_notes" rows="3">{{ $siniestro->inspector_notes ?? '' }}</textarea>
                </div>

                <input id="id" type="hidden" value="{{ $siniestro->id ?? '' }}">
            </div>
        </div>

        {{-- Actions --}}
        <div class="d-flex flex-wrap gap-2">
            <button type="submit" class="btn btn-success px-4">
                <i class="fas fa-check me-2"></i>
                Aplicar cambios
            </button>

            {{-- si lo querés activo --}}
            {{-- <button type="button" class="btn btn-outline-primary px-4" onclick="CorreoEdu(event)">
                <i class="fas fa-paper-plane me-2"></i>
                Enviar IP
            </button> --}}
        </div>

    </form>
</section>

{{-- MODAL PRODUCTORES --}}
<div class="modal fade" id="modal_productores" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Productores</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle productores" id="productores" style="width:100%">
                        <thead>
                            <tr>
                                <th class="d-none">ID</th>
                                <th>Productor</th>
                                <th>Teléfono</th>
                                <th>E-mail</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($productores as $productor)
                                <tr data-id="{{ $productor->id }}" class="pick-productor">
                                    <td class="d-none">{{ $productor->id }}</td>
                                    <td>{{ $productor->name }}</td>
                                    <td>{{ $productor->phone }}</td>
                                    <td>{{ $productor->email }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end mt-3">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Confirmar</button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL TALLERES --}}
<div class="modal fade" id="modal_talleres" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Talleres homologados</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle talleres" id="talleres" style="width:100%">
                        <thead>
                            <tr>
                                <th class="d-none">ID</th>
                                <th>Taller</th>
                                <th>Localidad</th>
                                <th>Domicilio</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($talleres as $taller)
                                <tr data-id="{{ $taller->id }}" class="pick-taller">
                                    <td class="d-none">{{ $taller->id }}</td>
                                    <td>{{ $taller->name }}</td>
                                    <td>{{ $taller->city }}</td>
                                    <td>{{ $taller->address }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end mt-3">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Confirmar</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


@section('javas')
<script>
$(function () {
    // Check patente
    $('#license_plate').on('blur', function(){
        const patente = $(this).val();
        const _token = $('meta[name="csrf-token"]').attr('content');

        $.ajax({
            url: "{{ route('claims.check') }}",
            method: "POST",
            data: { license_plate: patente, _token },
            success: function(result) {
                if (result === 'unique') {
                    $('#error_patente').html('<span class="badge text-bg-success">Primer siniestro con esta patente</span>');
                    $('#license_plate').removeClass('is-invalid').addClass('is-valid');
                } else {
                    $('#error_patente').html('<span class="badge text-bg-warning">Ya existe un siniestro con esta patente</span>');
                    $('#license_plate').removeClass('is-valid').addClass('is-invalid');
                }
            }
        });
    });

    // DataTables modales (sin romper el diseño)
    $('.productores').DataTable({
        pageLength: 15,
        pagingType: "simple",
        lengthChange: true,
        responsive: true,
        processing: true,
    });

    $('.talleres').DataTable({
        pageLength: 10,
        pagingType: "simple",
        lengthChange: true,
        responsive: true,
        processing: true,
    });

    // Selección de productor
    $(document).on('click', '.pick-productor', function(){
        $('.pick-productor').removeClass('row-selected');
        $(this).addClass('row-selected');

        const id = $(this).data('id');
        productorData(id);
    });

    // Selección de taller
    $(document).on('click', '.pick-taller', function(){
        $('.pick-taller').removeClass('row-selected');
        $(this).addClass('row-selected');

        const id = $(this).data('id');
        tallerData(id);
    });
});
</script>

{{-- Mantengo tus funciones tal cual (solo las dejo separadas) --}}
<script>
function productorData(id){
    $.ajax({
        type:"get",
        dataType:"json",
        url:"/producers/" + id + "/data",
        success: function(data){
            $('#producer_id').val(data.id);
            $('#producer_name').val(data.name);
        }
    })
}

function tallerData(id){
    $.ajax({
        type:"get",
        dataType:"json",
        url:"/workshops/" + id + "/data",
        success: function(data){
            $('#workshop_name').val(data.name);
            $('#email').val(data.email);
        }
    })
}

</script>
@endsection
