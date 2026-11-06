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
<section class="py-2 app-elevated-section">

    {{-- Header --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 app-elevated-heading">
        <div>
            <h3 class="mb-0 fw-bold">Cotizar siniestro</h3>
            <nav aria-label="breadcrumb" class="mt-1">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('claims.index') }}" class="text-decoration-none">Siniestros</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Cotizar</li>
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

    @can('terceros-siniestro')
    <form class="app-elevated-form" action="{{ route('claims.update', $siniestro->id) }}" method="POST" enctype="multipart/form-data">
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
                    <a href="{{ route('claims.show', $siniestro->id) }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-paperclip me-1"></i>
                        Adjuntos
                    </a>
                </div>
<div class="col-12 col-md-4">
                    <label for="claim_number" class="form-label">Siniestro</label>
                    <input type="text" name="claim_number" id="claim_number" class="form-control"
                           value="{{ $siniestro->claim_number ?? '' }}">
                </div>

                <div class="col-12 col-md-4">
                    <label for="analyst" class="form-label">Analista</label>
                    <input type="text" name="analyst" id="analyst" class="form-control"
                           value="{{ $siniestro->analyst ?? '' }}">
                </div>

                <div class="col-12 col-md-4">
                    <label for="third_party_name" class="form-label">Nombre del tercero</label>
                    <input type="text" name="third_party_name" id="third_party_name" class="form-control"
                           value="{{ $siniestro->third_party_name ?? '' }}">
                </div>

                <div class="col-12 col-md-4">
                    <label for="license_plate" class="form-label">Patente</label>
                    <input type="text" name="license_plate" id="license_plate" class="form-control"
                           value="{{ $siniestro->license_plate ?? '' }}">
                </div>

                <div class="col-12 col-md-4">
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
                <div class="col-12 col-md-4">
                    <label for="client_type" class="form-label">Tipo de cliente</label>
                    <select class="form-select" id="client_type" name="client_type">
                        <option value="Tercero" {{ ($siniestro->client_type ?? '') === 'Tercero' ? 'selected' : '' }}>Tercero</option>
                    </select>
                </div>

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
                    <label for="reason" class="form-label">Motivo</label>
                    <select class="form-select" id="reason" name="reason">
                        <option value="{{ $siniestro->reason ?? '' }}" selected>
                            {{ $siniestro->reason ?? 'Seleccionar...' }}
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
                    <label for="notes" class="form-label">Observaciones</label>
                    <textarea class="form-control" name="notes" id="notes" rows="3">{{ $siniestro->notes ?? '' }}</textarea>
                </div>

                {{-- Taller --}}
                <div class="col-12 col-md-4">
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

                <div class="col-12 col-md-4">
                    <label for="email" class="form-label">E-mail</label>
                    <input type="text" name="email" class="form-control" id="email"
                           value="{{ $siniestro->email ?? '' }}">
                </div>

                <div class="col-12 col-md-4">
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

                <input type="hidden" name="coordinator" id="coordinator" value="{{ auth()->user()->name }}">

                <div class="col-12 col-md-4">
                    <label for="inspector" class="form-label">Asignado a inspector</label>
                    <select name="inspector_id" id="inspector_id" class="form-select">
                        <option value="">Sin asignar</option>
                        @foreach($users as $inspector)
                            <option value="{{ $inspector->id }}" @selected($siniestro->inspector_id == $inspector->id)>{{ $inspector->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-4">
                    <label for="status" class="form-label">Estado</label>
                    <select class="form-select" id="status" name="status">
                        <option value="{{ $siniestro->status ?? '' }}" selected>
                            {{ $siniestro->status ?? 'Seleccionar...' }}
                        </option>
                        <option value="Tercero-gestion">En gestión</option>
                        <option value="Cargar ampliacion">Cargar ampliacion</option>
                        <option value="Baja">Baja</option>
                    </select>
                </div>

                <div class="col-12 col-md-4">
                    <label for="inspection_date" class="form-label">Fecha IP</label>
                    <input type="date" name="inspection_date" class="form-control" id="inspection_date"
                           value="{{ $siniestro->inspection_date?->format('Y-m-d') ?? '' }}">
                </div>

                <div class="col-12 col-md-4">
                    <label for="inspection_time" class="form-label">Rango horario</label>
                    <input type="text" name="inspection_time" class="form-control" id="inspection_time"
                           value="{{ $siniestro->inspection_time ?? '' }}">
                </div>

                <div class="col-12 col-md-4">
                    <label for="file" class="form-label">Adjuntos</label>
                    <input type="file" name="urls[]" id="file" class="form-control" multiple>
                </div>

                <div class="col-12 col-md-4">
                    <label for="inspector_notes" class="form-label">Cargar fotos</label>
                    <button type="submit" class="btn btn-warning w-100 mt-2">
                        <i class="fas fa-upload me-1"></i>
                        Cargar fotos
                    </button>
                </div>

                <div class="col-12">
                    <label for="inspector_notes" class="form-label">Comentarios para el perito</label>
                    <textarea class="form-control" name="inspector_notes" id="inspector_notes" rows="3">{{ $siniestro->inspector_notes ?? '' }}</textarea>
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
                    <label for="negotiation" class="form-label">Negociación</label>
                    <input type="text" name="negotiation" class="form-control" id="negotiation"
                           value="{{ $siniestro->negotiation ?? '' }}">
                </div>

                <div class="col-12 col-md-4">
                    <label for="estimate" class="form-label">Presupuesto</label>
                    <input type="text" name="estimate" class="form-control" id="estimate"
                           value="{{ $siniestro->estimate ?? '' }}">
                </div>

                <div class="col-12 col-md-4">
                    <label for="quoted" class="form-label">Cotizado</label>
                    <input type="text" name="quoted" class="form-control" id="quoted"
                           value="{{ $siniestro->quoted ?? '' }}">
                </div>

                <div class="col-12 col-md-4">
                    <label for="first_offer" class="form-label">Ofrecimiento N°1</label>
                    <input type="text" name="first_offer" class="form-control" id="first_offer"
                           value="{{ $siniestro->first_offer ?? '' }}">
                </div>

                <div class="col-12 col-md-4">
                    <label for="second_offer" class="form-label">Ofrecimiento N°2</label>
                    <input type="text" name="second_offer" class="form-control" id="second_offer"
                           value="{{ $siniestro->second_offer ?? '' }}">
                </div>

                <div class="col-12 col-md-4">
                    <label for="closure" class="form-label">Cierre</label>
                    <input type="text" name="closure" class="form-control" id="closure"
                           value="{{ $siniestro->closure ?? '' }}">
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
                                    <td>{{ $taller->name }}</td>
                                    <td>{{ $taller->phone }}</td>
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
        lengthChange: true,
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
        url:"/workshops/" + id + "/data",
        success: function(data){
            $('#workshop_name').val(data.name);
            $('#email').val(data.email);
        }
    })
}

function updateData(event){
    event.preventDefault();
    var id = $('#id').val();
    var link = $('#link').val();
    var siniestro = $('#claim_number').val();
    var patente = $('#license_plate').val();
    var nrocorto = $('#short_number').val();
    var cliente = $('#client_type').val();
    var modalidad = $('#inspection_mode').val();
    var motivo = $('#reason').val();
    var correo = $('#email').val();
    var observaciones = $('#notes').val();
    var email = $('#email').val();
    var nombretaller = $('#workshop_name').val();
    var telefonos = $('#phone').val();
    var direccion = $('#address').val();
    var localidad = $('#city').val();
    var estado = $('#status').val();
    var fechaip = $('#inspection_date').val();
    var enviarorden = $('#send_order').val();
    var horario = $('#inspection_time').val();
    var comentariosparaip = $('#inspector_notes').val();
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
            producer_id: $('#producer_id').val(), inspection_mode:modalidad, reason:motivo, inspection_date:fechaip, license_plate:patente, claim_number:siniestro,
            city:localidad, address:direccion, email:email, status:estado,
            notes:observaciones, workshop_name:nombretaller, phone:telefonos,
            send_order:enviarorden, inspection_time:horario, inspector_notes:comentariosparaip,
            link:link, short_number:nrocorto, client_type:cliente,
            comentariosdelperitovisita:comentariosdelperitovisita, 
            comentariosdelperitofinales:comentariosdelperitofinales
        },
        url: "/claims/" + id + "/data",
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
