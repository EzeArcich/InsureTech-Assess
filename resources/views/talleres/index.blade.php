@extends('layouts.app')
@section('page-title', 'Todos los talleres')
@section('page-description', 'Talleres homologados y no homologados.')

@section('header-tools')
<label class="claims-global-search" for="claims-global-search"><i class="bi bi-search"></i><input id="claims-global-search" type="search" placeholder="Buscar taller, localidad o email…"></label>
@endsection

@section('content')
<x-table-panel>
    <x-slot:actions>
@hasanyrole('CEO|Administrativo')
<a class="claims-create" href="{{ route('workshops.create') }}"><i class="bi bi-plus-lg"></i> Nuevo taller</a>
@endhasanyrole
    </x-slot:actions>
<table class="table align-middle w-100 app-data-table" data-standard-table>
                <thead class="table-success">
                    <tr>
                        <th class="d-none">ID</th>
                        <th>Taller</th>
                        <th>Homologación</th><th>Horarios</th><th>Dirección</th>
                        <th>Barrio</th>
                        <th>Localidad</th>
                        <th>Teléfonos</th>
                        <th>E-mail</th>
                        <th>Zona</th>
                        <th>Grúa</th>
                        <th>Traslado</th>
                        <th>Granizo</th>
                        <th>Pone repuestos</th>
                        <th>Vacaciones</th>
                        <th>Desc. franquicia</th>
                        @hasanyrole('CEO|Administrativo')
                        <th class="text-center" style="width: 140px;">Acciones</th>
                        @endhasanyrole
                    </tr>
                </thead>
                
                <tbody>
                    @foreach ($talleres as $taller)
                        <tr>
                            <td class="d-none">{{ $taller->id }}</td>
                            <td class="fw-semibold">{{ $taller->name }}</td>
                            <td><span class="role-badge">{{ $taller->is_approved ? "Homologado" : "No homologado" }}</span></td><td>{{ $taller->opening_hours }}</td><td>{{ $taller->address }}</td>
                            <td>{{ $taller->neighborhood }}</td>
                            <td>{{ $taller->city }}</td>
                            <td>{{ $taller->phone }}</td>
                            <td>{{ $taller->email }}</td>
                            <td>{{ $taller->area }}</td>
                            <td>{{ $taller->towing }}</td>
                            <td>{{ $taller->transport }}</td>
                            <td>{{ $taller->hail }}</td>
                            <td>{{ $taller->repair_availability }}</td>
                            <td>{{ $taller->holidays }}</td>
                            <td>{{ $taller->deductible_discount }}</td>
                            @hasanyrole('CEO|Administrativo')
                            <td>
                                <div class="table-actions justify-content-center">
                                    <a href="{{ route('workshops.edit', $taller->id) }}"
                                       class="btn btn-palette-blue btn-sm px-3">
                                        <i class="fas fa-pen me-1"></i>
                                        Editar
                                    </a>

                                    <form action="{{ route('workshops.destroy', $taller->id) }}"
                                          method="POST"
                                          class="m-0 p-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="btn btn-palette-orange btn-sm px-3"
                                                onclick="return confirm('¿Seguro que querés borrar este taller?')">
                                            <i class="fas fa-trash me-1"></i>
                                            Borrar
                                        </button>
                                    </form>
                                </div>
                            </td>
                            @endhasanyrole
                        </tr>
                    @endforeach
                </tbody>
            </table>
</x-table-panel>
@endsection
