@extends('layouts.app')

@push('styles')
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
            <h3 class="mb-0 fw-bold">Cotizar siniestro</h3>
            <nav aria-label="breadcrumb" class="mt-1">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ url('/siniestros') }}" class="text-decoration-none">Siniestros</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Cotizar</li>
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

    @can('terceros-siniestro')
    <form action="{{ route('siniestros.update', $siniestro->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

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

                <div class="col-12 d-flex justify-content-between align-items-center">
                    <label for="link" class="form-label mb-0">Link 2.0</label>
                    <a href="{{ route('siniestros.show', $siniestro->id) }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-paperclip me-1"></i>
                        Adjuntos
                    </a>
                </div>
                <div class="col-12">
                    <input type="text" name="link" id="link" class="form-control"
                           value="{{ $siniestro->link ?? '' }}">
                </div>

                <div class="col-12 col-md-4">
                    <label for="siniestro" class="form-label">Siniestro</label>
                    <input type="text" name="siniestro" id="siniestro" class="form-control"
                           value="{{ $siniestro->siniestro ?? '' }}">
                </div>

                <div class="col-12 col-md-4">
                    <label for="analista" class="form-label">Analista</label>
                    <input type="text" name="analista" id="analista" class="form-control"
                           value="{{ $siniestro->analista ?? '' }}">
                </div>

                <div class="col-12 col-md-4">
                    <label for="nombretercero" class="form-label">Nombre del tercero</label>
                    <input type="text" name="nombretercero" id="nombretercero" class="form-control"
                           value="{{ $siniestro->nombretercero ?? '' }}">
                </div>

                <div class="col-12 col-md-4">
                    <label for="patente" class="form-label">Patente</label>
                    <input type="text" name="patente" id="patente" class="form-control"
                           value="{{ $siniestro->patente ?? '' }}">
                </div>

                <div class="col-12 col-md-4">
                    <label for="compania" class="form-label">Compañía</label>
                    <input type="text" name="compania" id="compania" class="form-control"
                           value="{{ $siniestro->compania ?? '' }}">
                </div>

                <div class="col-12 col-md-4">
                    <label for="nrocorto" class="form-label">Nro. corto</label>
                    <input type="text" name="nrocorto" id="nrocorto" class="form-control"
                           value="{{ $siniestro->nrocorto ?? '' }}">
                </div>

                <div class="col-12 col-md-4">
                    <label for="cliente" class="form-label">Cliente</label>
                    <select class="form-select" id="cliente" name="cliente">
                        <option value="Tercero" {{ ($siniestro->cliente ?? '') === 'Tercero' ? 'selected' : '' }}>Tercero</option>
                    </select>
                </div>

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
                    <label for="motivo" class="form-label">Motivo</label>
                    <select class="form-select" id="motivo" name="motivo">
                        <option value="{{ $siniestro->motivo ?? '' }}" selected>
                            {{ $siniestro->motivo ?? 'Seleccionar...' }}
                        </option>
                        <option value="Reclamo">Reclamo</option>
                    </select>
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
                <div class="col-12 col-md-4">
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

                <div class="col-12 col-md-4">
                    <label for="email" class="form-label">E-mail</label>
                    <input type="text" name="email" class="form-control" id="email"
                           value="{{ $siniestro->email ?? '' }}">
                </div>

                <div class="col-12 col-md-4">
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

                <input type="hidden" name="coordinador" id="coordinador" value="{{ auth()->user()->name }}">

                <div class="col-12 col-md-4">
                    <label for="inspector" class="form-label">Asignado a inspector</label>
                    <input type="text" name="inspector" class="form-control" id="inspector"
                           value="{{ $siniestro->inspector ?? '' }}">
                </div>

                <div class="col-12 col-md-4">
                    <label for="estado" class="form-label">Estado</label>
                    <select class="form-select" id="estado" name="estado">
                        <option value="{{ $siniestro->estado ?? '' }}" selected>
                            {{ $siniestro->estado ?? 'Seleccionar...' }}
                        </option>
                        <option value="Tercero-gestion">En gestión</option>
                        <option value="Cargar ampliacion">Cargar ampliacion</option>
                        <option value="Baja">Baja</option>
                    </select>
                </div>

                <div class="col-12 col-md-4">
                    <label for="fechaip" class="form-label">Fecha IP</label>
                    <input type="date" name="fechaip" class="form-control" id="fechaip"
                           value="{{ $siniestro->fechaip ?? '' }}">
                </div>

                <div class="col-12 col-md-4">
                    <label for="horario" class="form-label">Rango horario</label>
                    <input type="text" name="horario" class="form-control" id="horario"
                           value="{{ $siniestro->horario ?? '' }}">
                </div>

                <div class="col-12 col-md-4">
                    <label for="file" class="form-label">Adjuntos</label>
                    <input type="file" name="urls[]" id="file" class="form-control" multiple>
                </div>

                <div class="col-12 col-md-4">
                    <label for="comentariosparaip" class="form-label">Cargar fotos</label>
                    <button type="submit" class="btn btn-warning w-100 mt-2">
                        <i class="fas fa-upload me-1"></i>
                        Cargar fotos
                    </button>
                </div>

                <div class="col-12">
                    <label for="comentariosparaip" class="form-label">Comentarios para el perito</label>
                    <textarea class="form-control" name="comentariosparaip" id="comentariosparaip" rows="3">{{ $siniestro->comentariosparaip ?? '' }}</textarea>
                </div>

            </div>
        </div>

        {{-- Panel 3: Negociación --}}
        <div class="panel mb-3">
            <div class="d-flex align-items-center justify-content-between mb-3 panel-header-navy">
                <div>
                    <h5 class="mb-0 fw-bold">Negociación</h5>
                    <small class="text-muted">Información de cotización y negociación</small>
                </div>
                <span class="badge text-bg-light border">Negociación</span>
            </div>

            <div class="row g-3">

                <div class="col-12 col-md-4">
                    <label for="negociacion" class="form-label">Negociación</label>
                    <input type="text" name="negociacion" class="form-control" id="negociacion"
                           value="{{ $siniestro->negociacion ?? '' }}">
                </div>

                <div class="col-12 col-md-4">
                    <label for="presupuesto" class="form-label">Presupuesto</label>
                    <input type="text" name="presupuesto" class="form-control" id="presupuesto"
                           value="{{ $siniestro->presupuesto ?? '' }}">
                </div>

                <div class="col-12 col-md-4">
                    <label for="cotizado" class="form-label">Cotizado</label>
                    <input type="text" name="cotizado" class="form-control" id="cotizado"
                           value="{{ $siniestro->cotizado ?? '' }}">
                </div>

                <div class="col-12 col-md-4">
                    <label for="ofrecimiento1" class="form-label">Ofrecimiento N°1</label>
                    <input type="text" name="ofrecimiento1" class="form-control" id="ofrecimiento1"
                           value="{{ $siniestro->ofrecimiento1 ?? '' }}">
                </div>

                <div class="col-12 col-md-4">
                    <label for="ofrecimiento2" class="form-label">Ofrecimiento N°2</label>
                    <input type="text" name="ofrecimiento2" class="form-control" id="ofrecimiento2"
                           value="{{ $siniestro->ofrecimiento2 ?? '' }}">
                </div>

                <div class="col-12 col-md-4">
                    <label for="cierre" class="form-label">Cierre</label>
                    <input type="text" name="cierre" class="form-control" id="cierre"
                           value="{{ $siniestro->cierre ?? '' }}">
                </div>

                <input id="id" type="hidden" value="{{ $siniestro->id ?? '' }}">
            </div>
        </div>

        {{-- Actions --}}
        <div class="d-flex flex-wrap gap-2">
            <button type="submit" class="btn btn-success px-4" onclick="updateData(event)">
                <i class="fas fa-check me-2"></i>
                Aplicar cambios
            </button>
        </div>

    </form>
    @endcan
</section>

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
                                <th>Teléfono</th>
                                <th>E-mail</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($talleres as $taller)
                                <tr data-id="{{ $taller->id }}" class="pick-taller">
                                    <td class="d-none">{{ $taller->id }}</td>
                                    <td>{{ $taller->taller }}</td>
                                    <td>{{ $taller->telefonos }}</td>
                                    <td>{{ $taller->email }}</td>
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
    // DataTables modales
    $('.talleres').DataTable({
        pageLength: 10,
        pagingType: "simple",
        lengthChange: false,
        responsive: true,
        processing: true,
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

<script>
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

function updateData(event){
    event.preventDefault();
    var id = $('#id').val();
    var link = $('#link').val();
    var siniestro = $('#siniestro').val();
    var patente = $('#patente').val();
    var nrocorto = $('#nrocorto').val();
    var cliente = $('#cliente').val();
    var modalidad = $('#modalidad').val();
    var motivo = $('#motivo').val();
    var correo = $('#correo').val();
    var observaciones = $('#observaciones').val();
    var email = $('#email').val();
    var nombretaller = $('#nombretaller').val();
    var telefonos = $('#telefono').val();
    var direccion = $('#direccion').val();
    var localidad = $('#localidad').val();
    var estado = $('#estado').val();
    var fechaip = $('#fechaip').val();
    var enviarorden = $('#enviarorden').val();
    var horario = $('#horario').val();
    var comentariosparaip = $('#comentariosparaip').val();
    var comentariosdelperitovisita = $('#comentariosdelperitovisita').val();
    var comentariosdelperitofinales = $('#comentariosdelperitofinales').val();

    $.ajaxSetup({
        headers:{
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    })

    $.ajax({
        type: "PUT",
        data: {
            modalidad:modalidad, motivo:motivo, fechaip:fechaip, patente:patente, siniestro:siniestro, 
            localidad:localidad, direccion:direccion, email:email, estado:estado,
            observaciones:observaciones, nombretaller:nombretaller, telefono:telefonos, 
            enviarorden:enviarorden, horario:horario, comentariosparaip:comentariosparaip, 
            link:link, nrocorto:nrocorto, cliente:cliente, 
            comentariosdelperitovisita:comentariosdelperitovisita, 
            comentariosdelperitofinales:comentariosdelperitofinales
        },
        url: "/teacher/update/"+id,
        success: function(response){
            Swal.fire({
                icon: 'success',
                position: 'top-end',
                showConfirmButton: false,
                title: 'Siniestro asignado con éxito',
                timer: 1200
            });
        }
    })
}
</script>
@endsection
