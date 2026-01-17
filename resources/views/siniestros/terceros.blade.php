@extends('layouts.app')

@section('content')
<section class="py-2">

    {{-- Header --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h3 class="mb-0 fw-bold">Reclamos de terceros</h3>
            <nav aria-label="breadcrumb" class="mt-1">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ url('/siniestros') }}" class="text-decoration-none">Siniestros</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Terceros</li>
                </ol>
            </nav>
        </div>

        @can('terceros-siniestro')
            <a class="btn btn-brand btn-sm" href="{{ route('siniestros.create') }}">
                <i class="fas fa-plus me-2"></i>
                Nuevo
            </a>
        @endcan
    </div>

    {{-- Panel principal --}}
    <div class="panel">
        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle mb-0 tablita w-100">
                <thead>
                    <tr>
                        <th class="d-none">ID</th>
                        
                        @can('perito-siniestro')
                            <th>Fecha IP</th>
                            <th>Nro Corto</th>
                        @endcan
                        
                        <th>Siniestro</th>
                        <th>Patente</th>
                        
                        @can('derivar-siniestro')
                            <th>Fecha IP</th>
                        @endcan
                        
                        @can('ver-siniestro')
                            <th>Coordinador</th>
                            <th>Actualizado</th>
                            <th>Fecha ingreso</th>
                            <th>Fecha gestión</th>
                            <th class="text-start">Observaciones</th>
                        @endcan
                        
                        <th>Estado</th>
                        <th>Modalidad</th>
                        
                        @can('derivar-siniestro')
                            <th>Cliente</th>
                            <th>Dirección</th>
                            <th>Localidad</th>
                            <th>Inspector</th>
                            <th>Motivo</th>
                        @endcan
                        
                        <th class="text-center" style="width: 140px;">Acciones</th>
                    </tr>
                </thead>
                
                <tbody>
                    @foreach ($siniestros as $siniestro)
                        <tr>
                            <td class="d-none">{{ $siniestro->id }}</td>
                            
                            @can('perito-siniestro')
                                <td>{{ $siniestro->fechaip }}</td>
                                <td>{{ $siniestro->nrocorto }}</td>
                            @endcan
                            
                            <td class="fw-semibold">{{ $siniestro->siniestro }}</td>
                            <td>{{ $siniestro->patente }}</td>
                            
                            @can('derivar-siniestro')
                                <td>{{ $siniestro->fechaip }}</td>
                            @endcan
                            
                            @can('ver-siniestro')
                                <td>{{ $siniestro->creator->name ?? '-' }}</td>
                                <td>{{ $siniestro->editor->name ?? '-' }}</td>
                                <td>{{ $siniestro->created_at ? date('d-m-Y', strtotime($siniestro->created_at)) : '-' }}</td>
                                <td>{{ $siniestro->updated_at ? date('d-m-Y', strtotime($siniestro->updated_at)) : '-' }}</td>
                                <td class="text-start" style="min-width: 220px;">
                                    <span class="d-inline-block text-truncate" style="max-width: 420px;">
                                        {{ $siniestro->observaciones }}
                                    </span>
                                </td>
                            @endcan
                            
                            <td>
                                <span class="badge text-bg-light border">
                                    {{ $siniestro->estado }}
                                </span>
                            </td>
                            <td>{{ $siniestro->modalidad }}</td>
                            
                            @can('derivar-siniestro')
                                <td>{{ $siniestro->cliente }}</td>
                                <td>{{ $siniestro->direccion }}</td>
                                <td>{{ $siniestro->localidad }}</td>
                                <td>{{ $siniestro->inspector }}</td>
                                <td>{{ $siniestro->motivo }}</td>
                            @endcan
                            
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
            paging: false,
            info: false,
            searching: true,
            order: [],
            language: {
                lengthMenu: '_MENU_'
            }
        });
    });
</script>
@endsection
