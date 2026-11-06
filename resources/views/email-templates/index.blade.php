@extends('layouts.app')
@section('page-title', 'Plantillas de correo')
@section('page-description', 'Mensajes con formato, variables dinámicas e historial de versiones.')
@section('content')
<x-table-panel><x-slot:actions><a class="claims-create" href="{{ route('email-templates.create') }}">Nueva plantilla</a></x-slot:actions>
<table class="table app-data-table w-100" data-standard-table><thead><tr><th>Nombre</th><th>Estado</th><th>Versión</th><th>Asunto</th><th>Acciones</th></tr></thead><tbody>@foreach($templates as $template)<tr><td>{{ $template->name }}</td><td><span class="role-badge">{{ $template->is_active ? 'Activa' : 'Inactiva' }}</span></td><td>{{ $template->latestVersion?->number }}</td><td>{{ $template->latestVersion?->subject }}</td><td><div class="table-actions"><a class="btn btn-palette-gray btn-sm" href="{{ route('email-templates.show', $template) }}">Historial</a><a class="btn btn-palette-blue btn-sm" href="{{ route('email-templates.edit', $template) }}">Editar</a>@if($template->is_active)<form method="POST" action="{{ route('email-templates.destroy', $template) }}">@csrf @method('DELETE')<button class="btn btn-palette-orange btn-sm">Desactivar</button></form>@endif</div></td></tr>@endforeach</tbody></table></x-table-panel>
@endsection
