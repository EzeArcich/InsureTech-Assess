@extends('layouts.app')

@section('page-title', 'Todos los siniestros')
@section('page-description', 'Listado de todos los siniestros en compañía y estado.')

@section('header-tools')
                <label class="claims-global-search" for="claims-global-search">
                    <i class="bi bi-search" aria-hidden="true"></i>
                    <input id="claims-global-search" type="search" placeholder="Buscar siniestro, compañía o asegurado…" autocomplete="off">
                </label>
@endsection

@section('content')
        <x-table-panel aria-label="Listado de siniestros">
            <x-slot:actions>
                @if(!empty(array_filter($filters)))
                    <a href="{{ route('claims.all') }}" class="btn btn-palette-gray btn-sm me-auto">Quitar filtros</a>
                    @if(!empty($filters['status']))
                        <span class="status-badge status-badge--progress me-3">{{ $filters['status'] === '__unset__' ? 'Sin estado' : ($filters['status'] === 'IPgestion' ? 'En gestión' : $filters['status']) }}</span>
                    @endif
                @endif
                @canany(['crear-siniestro', 'ceo-siniestro'])
                    <a href="{{ route('claims.create') }}" class="claims-create"><i class="bi bi-plus-lg"></i> Nuevo siniestro</a>
                @endcanany
            </x-slot:actions>
                <table id="all-claims" class="table align-middle w-100 app-data-table" data-source="{{ route('claims.all.data', $filters) }}" data-client-url="{{ route('clients.show', '__ID__') }}" data-inspector-url="{{ route('inspectors.show', '__ID__') }}">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="claims-select-all" aria-label="Seleccionar los siniestros de esta página"></th>
                            <th>ID</th>
                            <th>Siniestro</th>
                            <th>Compañía</th>
                            <th>Asegurado</th>
                            <th>Patente</th>
                            <th>Estado</th>
                            <th>Inspector</th>
                            <th>Ingreso</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
        </x-table-panel>
@endsection

@section('javas')
    @vite('resources/js/admin-claims.js')
@endsection
