@extends('layouts.app')
@section('page-title', auth()->user()->isRestrictedInspector() ? 'Mis inspecciones' : 'Siniestros')
@section('page-description', 'Listado de siniestros según el filtro seleccionado.')

@section('header-tools')
<label class="claims-global-search" for="claims-global-search"><i class="bi bi-search"></i><input id="claims-global-search" type="search" placeholder="Buscar en el listado…"></label>
@endsection

@section('content')
<x-table-panel>
    <x-slot:actions>
@if(auth()->user()->isRestrictedInspector())
    <form action="{{ route('claims.index') }}" method="GET" class="d-flex flex-wrap align-items-center gap-2 me-auto mb-3">
        <label for="inbox-state">Bandeja</label>
        <select id="inbox-state" name="status" class="form-select form-select-sm w-auto">
            <option value="">Todas mis inspecciones</option>
            <option value="Peritando" @selected(request('status') === 'Peritando')>Peritando</option>
            <option value="IPgestion" @selected(request('status') === 'IPgestion')>En gestión</option>
        </select>
        <label for="inbox-date">Fecha IP</label>
        <input id="inbox-date" name="date" type="date" class="form-control form-control-sm w-auto" value="{{ request('date') }}">
        <button class="btn btn-palette-green btn-sm" type="submit">Filtrar</button>
        <a class="btn btn-palette-gray btn-sm" href="{{ route('claims.index') }}">Limpiar</a>
    </form>
@endif
@can('crear-siniestro')
<a class="claims-create" href="{{ route('claims.create') }}"><i class="bi bi-plus-lg"></i> Nuevo siniestro</a>
@endcan
    </x-slot:actions>
<table class="table align-middle w-100 app-data-table" data-standard-table>
                <thead>
                    <tr>
                        <th class="d-none">ID</th>

                        <th>Siniestro</th>
                        <th>Asegurado</th>
                        <th>Inspector</th>
                        <th>Compañía</th>

                        @can('peritos-siniestro')
                            <th>Fecha IP</th>
                            <th>Nro Corto</th>
                            <th>Dirección</th>
                            <th>Localidad</th>
                        @endcan

                        <th>Patente</th>

                        @can('derivar-siniestro')
                            <th>Fecha IP</th>
                        @endcan

                        @can('ver-siniestro')
                            <th>Estado</th>
                            <th>Fecha ingreso</th>
                            <th>Fecha gestión</th>
                            <th class="text-start">Observaciones</th>
                        @endcan

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

                            <td class="fw-semibold">{{ $siniestro->claim_number }}</td>
                            @include('siniestros.partials._person_links')
                            <td>{{ $siniestro->company?->name }}</td>

                            @can('peritos-siniestro')
                                <td>{{ $siniestro->inspection_date ? date('d-m-Y', strtotime($siniestro->inspection_date)) : '-' }}</td>
                                <td>{{ $siniestro->short_number }}</td>
                                <td>{{ $siniestro->address }}</td>
                                <td>{{ $siniestro->city }}</td>
                            @endcan

                            <td>{{ $siniestro->license_plate }}</td>

                            @can('derivar-siniestro')
                                <td>{{ $siniestro->inspection_date ? date('d-m-Y', strtotime($siniestro->inspection_date)) : '-' }}</td>
                            @endcan

                            @can('ver-siniestro')
                                <td>
                                    {{-- Si querés lo hacemos badge por estado (pendiente/en gestión/cerrado) --}}
                                    @include('siniestros.partials._status')
                                </td>
                                <td>{{ $siniestro->created_at ? date('d-m-Y', strtotime($siniestro->created_at)) : '-' }}</td>
                                <td>{{ $siniestro->updated_at ? date('d-m-Y', strtotime($siniestro->updated_at)) : '-' }}</td>
                                <td class="text-start" style="min-width: 220px;">
                                    <span class="d-inline-block text-truncate" style="max-width: 420px;">
                                        {{ $siniestro->notes }}
                                    </span>
                                </td>
                            @endcan

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
                                        {{ auth()->user()->isRestrictedInspector() ? 'Gestionar' : 'Editar' }}
                                    </a>

                                    @unless(auth()->user()->isRestrictedInspector())
                                    <form action="{{ route('claims.destroy', $siniestro->id) }}"
                                          method="POST"
                                          class="m-0 p-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="btn btn-palette-orange btn-sm px-3"
                                                onclick="return confirm('¿Seguro que querés borrar este registro?')">
                                            <i class="fas fa-trash me-1"></i>
                                            Borrar
                                        </button>
                                    </form>
                                    @endunless
                                </div>
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
</x-table-panel>
@endsection
