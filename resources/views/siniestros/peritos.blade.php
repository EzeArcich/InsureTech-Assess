@extends('layouts.app')
@section('page-title', 'Inspecciones asignadas')
@section('page-description', 'Siniestros asignados al inspector.')

@section('header-tools')
<label class="claims-global-search" for="claims-global-search"><i class="bi bi-search"></i><input id="claims-global-search" type="search" placeholder="Buscar en el listado…"></label>
@endsection

@section('content')
<x-table-panel>
    <x-slot:actions>

    </x-slot:actions>
<table class="table align-middle w-100 app-data-table" data-standard-table>
                <thead>
                    <tr>
                        <th class="d-none">ID</th>
                        <th>Siniestro</th>
                        <th>Asegurado</th>
                        <th>Inspector</th>
                        <th>Compañía</th>
                        <th>Fecha IP</th>
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
                            <td class="fw-semibold">{{ $siniestro->claim_number }}</td>
                            @include('siniestros.partials._person_links')
                            <td>{{ $siniestro->company?->name }}</td>
                            <td>{{ $siniestro->inspection_date ? date('d-m-Y', strtotime($siniestro->inspection_date)) : '-' }}</td>
                            <td>{{ $siniestro->address }}</td>
                            <td>{{ $siniestro->city }}</td>
                            <td>{{ $siniestro->license_plate }}</td>
                            <td>
                                <span class="badge text-bg-light border">
                                    {{ $siniestro->inspection_mode }}
                                </span>
                            </td>
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
