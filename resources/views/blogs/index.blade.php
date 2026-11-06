@extends('layouts.app')
@section('page-title', 'Noticias')
@section('page-description', 'Listado de noticias y publicaciones.')

@section('header-tools')
<label class="claims-global-search" for="claims-global-search"><i class="bi bi-search"></i><input id="claims-global-search" type="search" placeholder="Buscar en el listado…"></label>
@endsection

@section('content')
<x-table-panel>
    <x-slot:actions>
@can('crear-blog')
<a class="claims-create" href="{{ route('blogs.create') }}"><i class="bi bi-plus-lg"></i> Nueva noticia</a>
@endcan
    </x-slot:actions>
<table class="table align-middle w-100 app-data-table" data-standard-table>
                                <thead>
                                    <tr><th class="d-none">ID</th>
                                    <th>Titulo</th>
                                    <th>Contenido</th>
                                    <th>Acciones</th>
                              </tr></thead>
                              <tbody>
                            @foreach ($blogs as $blog)
                            <tr>
                                <td class="d-none">{{ $blog->id }}</td>
                                <td>{{ $blog->title }}</td>
                                <td>{{ $blog->content }}</td>
                                <td>
                                    <form action="{{ route('blogs.destroy',$blog->id) }}" method="POST" class="table-actions m-0">
                                        @can('editar-blog')
                                        <a class="btn btn-palette-blue btn-sm" href="{{ route('blogs.edit',$blog->id) }}">Editar</a>
                                        @endcan

                                        @csrf
                                        @method('DELETE')
                                        @can('borrar-blog')
                                        <button type="submit" class="btn btn-palette-orange btn-sm">Borrar</button>
                                        @endcan
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                            </tbody>
                        </table>
</x-table-panel>
@endsection
