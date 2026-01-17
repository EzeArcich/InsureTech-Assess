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
<section class="py-2">

    {{-- Header --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h3 class="mb-0 fw-bold">Ingresar siniestro</h3>
            <nav aria-label="breadcrumb" class="mt-1">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ url('/siniestros') }}" class="text-decoration-none">Siniestros</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Crear</li>
                </ol>
            </nav>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ url('/siniestros') }}" class="btn btn-light border btn-sm rounded-3">
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

    <form action="{{ route('siniestros.store') }}" method="POST" enctype="multipart/form-data">
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

                <div class="col-12">
                    <label for="link" class="form-label">Link 2.0</label>
                    <input type="text" name="link" id="link" class="form-control"
                           value="{{ $siniestro->link ?? '' }}">
                </div>

                <div class="col-12 col-md-3">
                    <label for="siniestro" class="form-label">Siniestro</label>
                    <input type="text" name="siniestro" id="siniestro" class="form-control"
                           value="{{ $siniestro->siniestro ?? '' }}">
                </div>

                <div class="col-12 col-md-3">
                    <label for="patente" class="form-label">Patente</label>
                    <input type="text" name="patente" id="patente" class="form-control"
                           value="{{ $siniestro->patente ?? '' }}">
                    <div id="error_patente" class="form-text"></div>
                </div>

                <div class="col-12 col-md-3">
                    <label for="compania" class="form-label">Compañía</label>
                    <input type="text" name="compania" id="compania" class="form-control"
                           value="{{ $siniestro->compania ?? '' }}">
                </div>

                <div class="col-12 col-md-3">
                    <label for="nrocorto" class="form-label">Nro. corto</label>
                    <input type="text" name="nrocorto" id="nrocorto" class="form-control"
                           value="{{ $siniestro->nrocorto ?? '' }}">
                </div>

                <div class="col-12 col-md-3">
                    <label for="cliente" class="form-label">Cliente</label>
                    <select class="form-select" id="cliente" name="cliente">
                        <option value="{{ $siniestro->cliente ?? '' }}" selected>
                            {{ $siniestro->cliente ?? 'Seleccionar...' }}
                        </option>
                        <option value="Asegurado">Asegurado</option>
                        <option value="Tercero">Tercero</option>
                        <option value="Cotizacion">Cotización</option>
                    </select>
                </div>

                <div class="col-12 col-md-3">
                    <label for="motivo" class="form-label">Motivo</label>
                    <select class="form-select" id="motivo" name="motivo">
                        <option value="{{ $siniestro->motivo ?? '' }}" selected>
                            {{ $siniestro->motivo ?? 'Seleccionar...' }}
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
                    <label for="nombre" class="form-label">Productor</label>
                    <div class="input-group">
                        <input type="text" class="form-control name" id="nombre" name="nombre"
                               value="{{ $siniestro->nombre ?? '' }}">
                        <button class="btn btn-outline-primary"
                                type="button"
                                data-bs-toggle="modal"
                                data-bs-target="#modal_productores">
                            <i class="fas fa-search-plus"></i>
                        </button>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <label for="emailPas" class="form-label">Email del PAS</label>
                    <input type="text" name="correo" class="form-control" id="emailPas"
                           value="{{ $siniestro->correo ?? '' }}">
                </div>

                <div class="col-12 col-md-6">
                    <label for="cc" class="form-label">En copia</label>
                    <input type="text" name="cc" class="form-control" id="cc" value="{{ $siniestro->cc ?? '' }}">
                </div>

                <div class="col-12 col-md-6">
                    <label for="cc2" class="form-label">Segundo en copia</label>
                    <input type="text" name="cc2" class="form-control" id="cc2" value="{{ $siniestro->cc2 ?? '' }}">
                </div>

                <div class="col-12">
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-outline-primary"
                                onclick="Correo(event)">
                            <i class="fas fa-envelope me-2"></i>
                            Contacto vía mail
                        </button>
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
                    <label for="observaciones" class="form-label">Observaciones</label>
                    <textarea class="form-control" name="observaciones" id="observaciones" rows="3">{{ $siniestro->observaciones ?? '' }}</textarea>
                </div>

                {{-- Taller --}}
                <div class="col-12 col-md-6">
                    <label for="nombretaller" class="form-label">Nombre del taller</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="nombretaller" name="nombretaller"
                               value="{{ $siniestro->nombretaller ?? '' }}">
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
                    <label for="telefono" class="form-label">Teléfono</label>
                    <input type="text" name="telefono" class="form-control" id="telefono"
                           value="{{ $siniestro->telefono ?? '' }}">
                </div>

                <div class="col-12 col-md-6">
                    <label for="direccion" class="form-label">Dirección</label>
                    <input type="text" name="direccion" class="form-control" id="direccion"
                           value="{{ $siniestro->direccion ?? '' }}">
                </div>

                <div class="col-12 col-md-6">
                    <label for="localidad" class="form-label">Localidad</label>
                    <input type="text" name="localidad" class="form-control" id="localidad"
                           value="{{ $siniestro->localidad ?? '' }}">
                </div>

                {{-- Coordinador hidden --}}
                <input type="hidden" name="coordinador" id="coordinador" value="{{ auth()->user()->name }}">

                <div class="col-12 col-md-4">
                    <label for="modalidad" class="form-label">Tipo de inspección</label>
                    <select class="form-select" id="modalidad" name="modalidad">
                        <option value="{{ $siniestro->modalidad ?? '' }}" selected>
                            {{ $siniestro->modalidad ?? 'Seleccionar...' }}
                        </option>
                        <option value="presencial">Presencial</option>
                        <option value="videollamada">Videollamada</option>
                        <option value="foto y presupuesto">Por foto y presupuesto</option>
                        <option value="foto">Por foto</option>
                    </select>
                </div>

                <div class="col-12 col-md-4">
                    <label for="estado" class="form-label">Estado</label>
                    <select class="form-select" id="estado" name="estado">
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
                    <label for="fechaip" class="form-label">Fecha IP</label>
                    <input type="date" name="fechaip" class="form-control" id="fechaip"
                           value="{{ $siniestro->fechaip ?? '' }}">
                </div>

                <div class="col-12 col-md-4">
                    <label for="enviarorden" class="form-label">Enviar orden</label>
                    <select class="form-select" id="enviarorden" name="enviarorden">
                        <option value="si" selected>Si</option>
                        <option value="no">No</option>
                    </select>
                </div>

                <div class="col-12 col-md-4">
                    <label for="horario" class="form-label">Rango horario</label>
                    <input type="text" name="horario" class="form-control" id="horario"
                           value="{{ $siniestro->horario ?? '' }}">
                </div>

                <div class="col-12 col-md-4">
                    <label for="imagen" class="form-label">Cover</label>
                    <input type="file" name="imagen" class="form-control">
                </div>

                <div class="col-12">
                    <label for="comentariosparaip" class="form-label">Comentarios para el perito</label>
                    <textarea class="form-control" name="comentariosparaip" id="comentariosparaip" rows="3">{{ $siniestro->comentariosparaip ?? '' }}</textarea>
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
                                    <td>{{ $productor->nombre }}</td>
                                    <td>{{ $productor->telefono }}</td>
                                    <td>{{ $productor->correo }}</td>
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
                                    <td>{{ $taller->taller }}</td>
                                    <td>{{ $taller->localidad }}</td>
                                    <td>{{ $taller->direccion }}</td>
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
    $('#patente').on('blur', function(){
        const patente = $(this).val();
        const _token = $('meta[name="csrf-token"]').attr('content');

        $.ajax({
            url: "{{ route('siniestros.check') }}",
            method: "POST",
            data: { patente, _token },
            success: function(result) {
                if (result === 'unique') {
                    $('#error_patente').html('<span class="badge text-bg-success">Primer siniestro con esta patente</span>');
                    $('#patente').removeClass('is-invalid').addClass('is-valid');
                } else {
                    $('#error_patente').html('<span class="badge text-bg-warning">Ya existe un siniestro con esta patente</span>');
                    $('#patente').removeClass('is-valid').addClass('is-invalid');
                }
            }
        });
    });

    // DataTables modales (sin romper el diseño)
    $('.productores').DataTable({
        pageLength: 15,
        pagingType: "simple",
        lengthChange: false,
        responsive: true,
        processing: true,
    });

    $('.talleres').DataTable({
        pageLength: 10,
        pagingType: "simple",
        lengthChange: false,
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
        url:"/teacher/productores/"+id,
        success: function(data){
            $('#nombre').val(data.nombre);
            $('#emailPas').val(data.correo);
        }
    })
}

function tallerData(id){
    $.ajax({
        type:"get",
        dataType:"json",
        url:"/teacher/taller/"+id,
        success: function(data){
            $('#nombretaller').val(data.taller);
            $('#telefono').val(data.telefonos);
            $('#email').val(data.email);
            $('#direccion').val(data.direccion);
            $('#localidad').val(data.localidad);
        }
    })
}

function Correo(event){
    event.preventDefault();

    const siniestro   = $('#siniestro').val();
    const emailPas    = $('#emailPas').val();
    const coordinador = $('#coordinador').val();
    const patente     = $('#patente').val();
    const cc          = $('#cc').val();
    const cc2         = $('#cc2').val();
    const nrocorto    = $('#nrocorto').val();

    $.ajaxSetup({
        headers:{ 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    $.ajax({
        type: "POST",
        data: { siniestro, emailPas, patente, coordinador, cc, cc2, nrocorto },
        url: "/correo",
        success: function(){
            Swal.fire({
                icon: 'success',
                position: 'top-end',
                showConfirmButton: false,
                title: 'Correo enviado con éxito',
                timer: 1200
            });
        }
    });
}
</script>
@endsection
