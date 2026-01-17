@extends('layouts.app')

@push('styles')
<style>
    /* Selección de fila en tablas */
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
            <h3 class="mb-0 fw-bold">Inspecciones a derivar</h3>
            <nav aria-label="breadcrumb" class="mt-1">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ url('/siniestros') }}" class="text-decoration-none">Siniestros</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Derivar</li>
                </ol>
            </nav>
        </div>

        <div class="d-flex gap-2">
            <span class="badge text-bg-primary">Total: {{ $cant_total }}</span>
        </div>
    </div>

    <div class="row g-3">
        {{-- Tabla de siniestros --}}
        <div class="col-12 col-lg-8">
            <div class="panel">
                <div class="d-flex align-items-center justify-content-between mb-3 panel-header-deep">
                    <div>
                        <h5 class="mb-0 fw-bold">Inspecciones para asignar</h5>
                        <small class="text-muted">Seleccioná un siniestro para asignar inspector</small>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-info btn-sm position-relative">
                            TH <span class="badge text-bg-light position-absolute top-0 start-100 translate-middle rounded-pill">{{ $cant_th }}</span>
                        </button>
                        <button class="btn btn-info btn-sm position-relative">
                            Foto <span class="badge text-bg-light position-absolute top-0 start-100 translate-middle rounded-pill">{{ $cant_foto }}</span>
                        </button>
                        <button class="btn btn-info btn-sm position-relative">
                            Foto/Pres. <span class="badge text-bg-light position-absolute top-0 start-100 translate-middle rounded-pill">{{ $cant_caba }}</span>
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle mb-0 siniestros w-100">
                        <thead>
                            <tr>
                                <th class="d-none">ID</th>
                                <th>Siniestro</th>
                                <th>Fecha IP</th>
                                <th>Modalidad</th>
                                <th>Patente</th>
                                <th>Domicilio</th>
                                <th>Localidad</th>
                                <th>Inspector</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($siniestros as $siniestro)
                                <tr data-id="{{ $siniestro->id }}" class="pick-siniestro">
                                    <td class="d-none">{{ $siniestro->id }}</td>
                                    <td class="fw-semibold">{{ $siniestro->siniestro }}</td>
                                    <td>{{ $siniestro->fechaip }}</td>
                                    <td>
                                        <span class="badge text-bg-light border">
                                            {{ $siniestro->modalidad }}
                                        </span>
                                    </td>
                                    <td>{{ $siniestro->patente }}</td>
                                    <td>{{ $siniestro->direccion }}</td>
                                    <td>{{ $siniestro->localidad }}</td>
                                    <td>{{ $siniestro->inspector ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Formulario de datos IP --}}
        <div class="col-12 col-lg-4">
            <div class="panel sticky-top" style="top: 20px;">
                <div class="d-flex align-items-center justify-content-between mb-3 panel-header-sage">
                    <div>
                        <h5 class="mb-0 fw-bold">Datos de IP</h5>
                        <small class="text-muted">Completá y asigná inspector</small>
                    </div>
                </div>

                <form id="formDerivar" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-12">
                            <label for="siniestro" class="form-label">Siniestro</label>
                            <input type="text" name="siniestro" id="siniestro" class="form-control">
                        </div>

                        <div class="col-12">
                            <label for="fechaip" class="form-label">Fecha de inspección</label>
                            <input type="date" name="fechaip" id="fechaip" class="form-control">
                        </div>

                        <div class="col-12">
                            <label for="direccion" class="form-label">Dirección</label>
                            <input type="text" name="direccion" id="direccion" class="form-control">
                        </div>

                        <div class="col-12">
                            <label for="localidad" class="form-label">Localidad</label>
                            <input type="text" name="localidad" id="localidad" class="form-control">
                        </div>

                        <div class="col-12">
                            <label for="patente" class="form-label">Patente</label>
                            <input type="text" name="patente" id="patente" class="form-control">
                        </div>

                        <div class="col-12">
                            <label for="inspector" class="form-label">Inspector</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="inspector" name="inspector">
                                <button class="btn btn-outline-primary"
                                        type="button"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modal_clientes">
                                    <i class="fas fa-search-plus"></i>
                                </button>
                            </div>
                        </div>

                        <div class="col-12">
                            <label for="email" class="form-label">Mail inspector</label>
                            <input type="text" name="email" id="email" class="form-control">
                        </div>

                        <input type="hidden" name="estado" id="estado" value="Peritando">
                        <input id="id" type="hidden">

                        <div class="col-12">
                            <button type="submit" class="btn btn-primary w-100" onclick="updateData(event)">
                                <i class="fas fa-check me-2"></i>
                                Aplicar cambios
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

{{-- MODAL INSPECTORES --}}
<div class="modal fade" id="modal_clientes" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Buscar perito</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle inspectores" id="inspectores" style="width:100%">
                        <thead>
                            <tr>
                                <th class="d-none">ID</th>
                                <th>Inspector</th>
                                <th>E-mail</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr data-id="{{ $user->id }}" class="pick-inspector">
                                    <td class="d-none">{{ $user->id }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
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
    // DataTables
    $('.siniestros').DataTable({
        pageLength: 10,
        pagingType: "simple",
        lengthChange: false,
        responsive: true,
        processing: true,
        rowCallback: function( row, data, index ) {
            var allData = this.api().column(4).data().toArray();
            if (allData.indexOf(data[4]) != allData.lastIndexOf(data[4])) {
                $(row).css('background-color', '#E8DE3E');
            }
        }
    });

    $('.inspectores').DataTable({
        pageLength: 10,
        pagingType: "simple",
        lengthChange: false,
        responsive: true,
        processing: true,
    });

    // Selección de siniestro
    $(document).on('click', '.pick-siniestro', function(){
        $('.pick-siniestro').removeClass('row-selected');
        $(this).addClass('row-selected');

        const id = $(this).data('id');
        editData(id);
    });

    // Selección de inspector
    $(document).on('click', '.pick-inspector', function(){
        $('.pick-inspector').removeClass('row-selected');
        $(this).addClass('row-selected');

        const id = $(this).data('id');
        userData(id);
    });
});
</script>

<script>
function editData(id){
    $.ajax({
        type:"get",
        dataType:"json",
        url:"/teacher/edit/"+id,
        success: function(data){
            $('#id').val(data.id);
            $('#siniestro').val(data.siniestro);
            $('#fechaip').val(data.fechaip);
            $('#patente').val(data.patente);
            $('#direccion').val(data.direccion);
            $('#localidad').val(data.localidad);
            $('#email').val(data.email || '');
        }
    })
}

function userData(id){
    $.ajax({
        type:"get",
        dataType:"json",
        url:"/teacher/users/"+id,
        success: function(data){
            $('#inspector').val(data.name);
            $('#email').val(data.email);
        }
    })
}

function updateData(event){
    event.preventDefault();
    var id = $('#id').val();
    var siniestro = $('#siniestro').val();
    var fechaip = $('#fechaip').val();
    var inspector = $('#inspector').val();
    var localidad = $('#localidad').val();
    var estado = $('#estado').val();
    var direccion = $('#direccion').val();
    var email = $('#email').val(); 
    var patente = $('#patente').val();

    $.ajaxSetup({
        headers:{
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    })

    $.ajax({
        type: "PUT",
        dataType: "json",
        data: {
            inspector:inspector, fechaip:fechaip, estado:estado, patente:patente, 
            siniestro:siniestro, localidad:localidad, direccion:direccion, email:email
        },
        url: "/teacher/update/"+id,
        success: function(response){
            setTimeout(function() {
                location.reload();
            }, 1200);
            
            Swal.fire({
                icon: 'success',
                position: 'top-end',
                showConfirmButton: false,
                title: 'Siniestro asignado con éxito',
                timer: 1500
            });
            
            // Limpiar formulario
            $('#id').val('');
            $('#siniestro').val('');
            $('#fechaip').val('');
            $('#inspector').val('');
            $('#localidad').val('');
            $('#direccion').val('');
            $('#email').val('');
            $('#patente').val('');
        }
    })
}
</script>
@endsection
