@extends('layouts.app')
@section('page-title', 'Reclamos de terceros')
@section('page-description', 'Siniestros de terceros.')

@section('header-tools')
<label class="claims-global-search" for="claims-global-search"><i class="bi bi-search"></i><input id="claims-global-search" type="search" placeholder="Buscar en el listado…"></label>
@endsection

@section('content')
<x-table-panel>
    <x-slot:actions>
@can('crear-siniestro')
<a class="claims-create" href="{{ route('claims.create') }}"><i class="bi bi-plus-lg"></i> Nuevo siniestro</a>
@endcan
    </x-slot:actions>
<table class="table align-middle w-100 app-data-table" data-standard-table>
                <thead>
                    <tr>
                        <th class="d-none">ID</th>
                        
                        @can('perito-siniestro')
                            <th>Fecha IP</th>
                            <th>Nro Corto</th>
                        @endcan
                        
                        <th>Siniestro</th>
                        <th>Asegurado</th>
                        <th>Inspector</th>
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
                                <td>{{ $siniestro->inspection_date }}</td>
                                <td>{{ $siniestro->short_number }}</td>
                            @endcan
                            
                            <td class="fw-semibold">{{ $siniestro->claim_number }}</td>
                            @include('siniestros.partials._person_links')
                            <td>{{ $siniestro->license_plate }}</td>
                            
                            @can('derivar-siniestro')
                                <td>{{ $siniestro->inspection_date }}</td>
                            @endcan
                            
                            @can('ver-siniestro')
                                <td>{{ $siniestro->creator->name ?? '-' }}</td>
                                <td>{{ $siniestro->editor->name ?? '-' }}</td>
                                <td>{{ $siniestro->created_at ? date('d-m-Y', strtotime($siniestro->created_at)) : '-' }}</td>
                                <td>{{ $siniestro->updated_at ? date('d-m-Y', strtotime($siniestro->updated_at)) : '-' }}</td>
                                <td class="text-start" style="min-width: 220px;">
                                    <span class="d-inline-block text-truncate" style="max-width: 420px;">
                                        {{ $siniestro->notes }}
                                    </span>
                                </td>
                            @endcan
                            
                            <td>
                                @include('siniestros.partials._status')
                            </td>
                            <td>{{ $siniestro->inspection_mode }}</td>
                            
                            @can('derivar-siniestro')
                                <td>{{ $siniestro->client_type }}</td>
                                <td>{{ $siniestro->address }}</td>
                                <td>{{ $siniestro->city }}</td>
                                <td>{{ $siniestro->reason }}</td>
                            @endcan
                            
                            <td>
                                <div class="table-actions justify-content-center">
                                    <a href="{{ route('claims.edit', $siniestro->id) }}"
                                       class="btn btn-palette-blue btn-sm px-3">
                                        <i class="fas fa-pen me-1"></i>
                                        Editar
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
</x-table-panel>
@endsection
