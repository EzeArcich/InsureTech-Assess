@extends('layouts.app')
@section('page-title', 'Roles')
@section('page-description', 'Administración de roles y permisos.')

@section('header-tools')
<label class="claims-global-search" for="claims-global-search"><i class="bi bi-search"></i><input id="claims-global-search" type="search" placeholder="Buscar en el listado…"></label>
@endsection

@section('content')
<x-table-panel>
    <x-slot:actions>
@hasrole('CEO')
<a class="claims-create" href="{{ route('roles.create') }}"><i class="bi bi-plus-lg"></i> Nuevo rol</a>
@endhasrole
    </x-slot:actions>
<table class="table align-middle w-100 app-data-table" data-standard-table>
                            <thead>
                                <tr>
                                    <th>Rol</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($roles as $role)
                                    <tr>
                                        <td>{{ $role->name }}</td>
                                        <td>
                                            <div class="table-actions">
                                            @hasrole('CEO')
                                                <a class="btn btn-palette-blue btn-sm" href="{{ route('roles.edit', $role->id) }}">
                                                    Editar
                                                </a>
                                            @endhasrole

                                            @hasrole('CEO')
                                                <form action="{{ route('roles.destroy', $role->id) }}"
                                                      method="POST" class="m-0">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-palette-orange btn-sm">
                                                        Borrar
                                                    </button>
                                                </form>
                                            @endhasrole
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
</x-table-panel>
@endsection
