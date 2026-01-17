@extends('layouts.app')

@section('content')
<section class="py-2">

    {{-- Header --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h3 class="mb-0 fw-bold">Inspecciones a peritar el día de hoy</h3>
            <nav aria-label="breadcrumb" class="mt-1">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ url('/siniestros') }}" class="text-decoration-none">Siniestros</a></li>
                    <li class="breadcrumb-item active" aria-current="page">IP a ser vistas</li>
                </ol>
            </nav>
        </div>
    </div>

    {{-- Panel principal --}}
    <div class="panel">
        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle mb-0 tablita w-100">
                <thead>
                    <tr>
                        <th class="d-none">ID</th>
                        <th>Siniestro</th>
                        <th>Compañía</th>
                        <th>Fecha IP</th>
                        <th>Nro Corto</th>
                        <th>Dirección</th>
                        <th>Localidad</th>
                        <th>Patente</th>
                        <th>Modalidad</th>
                        <th class="text-center" style="width: 140px;">Acciones</th>
                    </tr>
                </thead>
                
                <tbody>
                    @foreach ($siniestros as $siniestro)
                        <tr>
                            <td class="d-none">{{ $siniestro->id }}</td>
                            <td class="fw-semibold">{{ $siniestro->siniestro }}</td>
                            <td>{{ $siniestro->compania }}</td>
                            <td>{{ $siniestro->fechaip ? date('d-m-Y', strtotime($siniestro->fechaip)) : '-' }}</td>
                            <td>{{ $siniestro->nrocorto }}</td>
                            <td>{{ $siniestro->direccion }}</td>
                            <td>{{ $siniestro->localidad }}</td>
                            <td>{{ $siniestro->patente }}</td>
                            <td>
                                <span class="badge text-bg-light border">
                                    {{ $siniestro->modalidad }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex justify-content-center align-items-center gap-2">
                                    <a href="{{ route('siniestros.edit', $siniestro->id) }}"
                                       class="btn btn-outline-primary btn-sm px-3">
                                        <i class="fas fa-pen me-1"></i>
                                        Editar
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection

@section('javas')
<script>
    $(function () {
        $('.tablita').DataTable({
            pageLength: 50,
            responsive: true,
            processing: true,
            dom: 'Bfrtilp',
            buttons: [
                {
                    extend: 'print',
                    text: '<i class="fas fa-print"></i>',
                    titleAttr: 'Imprimir',
                    className: 'btn btn-lg btn-danger'
                }
            ],
            order: []
        });
    });
</script>
@endsection
