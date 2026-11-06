@extends('layouts.app')
@section('page-title', 'Usuarios')
@section('page-description', 'Administración de usuarios y roles.')

@section('header-tools')
<label class="claims-global-search" for="claims-global-search"><i class="bi bi-search"></i><input id="claims-global-search" type="search" placeholder="Buscar en el listado…"></label>
@endsection

@section('content')
<x-table-panel>
    <x-slot:actions>
@unless(auth()->user()->isRestrictedInspector())
<a class="claims-create" href="{{ route('users.create') }}"><i class="bi bi-plus-lg"></i> Nuevo usuario</a>
@endunless
    </x-slot:actions>
<table class="table align-middle w-100 app-data-table" data-standard-table>
                              <thead>
                                  <tr><th class="d-none">ID</th>
                                  <th>Nombre</th>
                                  <th>E-mail</th>
                                  <th>Teléfono</th>
                                  <th>Rol</th>
                                  <th>Acciones</th>
                              </tr></thead>
                              <tbody>
                                @foreach ($usuarios as $usuario)
                                  <tr>
                                    <td class="d-none">{{ $usuario->id }}</td>
                                    <td>{{ $usuario->name }}</td>
                                    <td>{{ $usuario->email }}</td>
                                    <td>{{ $usuario->phone }}</td>
                                    <td>
                                      <div class="d-flex flex-wrap gap-1">
                                          @forelse($usuario->getRoleNames() as $rolNombre)
                                              <span class="role-badge">{{ $rolNombre }}</span>
                                          @empty
                                              <span class="text-muted">Sin rol</span>
                                          @endforelse
                                      </div>
                                    </td>

                                    <td>
                                      <div class="table-actions">
                                      @unless(auth()->user()->isRestrictedInspector())
                                      <a class="btn btn-palette-blue btn-sm" href="{{ route('users.edit',$usuario->id) }}">Editar</a>
                                      @endunless

                                      @if(app()->environment('local') && auth()->id() !== $usuario->id)
                                              <form action="{{ route('users.impersonate', $usuario->id) }}" method="POST" class="m-0">
                                                  @csrf
                                                  <button type="submit" class="btn btn-palette-green btn-sm"><i class="bi bi-person-check me-1"></i>Ingresar como</button>
                                              </form>
                                      @endif

                                      @unless(auth()->user()->isRestrictedInspector())
                                      <form action="{{ route('users.destroy', $usuario->id) }}" method="POST" class="m-0">
                                          @csrf
                                          @method('DELETE')
                                          <button type="submit" class="btn btn-palette-orange btn-sm">
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
