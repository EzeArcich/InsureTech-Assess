@extends('layouts.app')
@section('page-title', $template->exists ? 'Editar plantilla de correo' : 'Nueva plantilla de correo')
@section('content')
@if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
<form class="panel" id="email-template-form" method="POST" action="{{ $template->exists ? route('email-templates.update', $template) : route('email-templates.store') }}">
@csrf @if($template->exists) @method('PUT') @endif
<input type="hidden" name="base_version" value="{{ old('base_version', $version?->number) }}">
<div class="row g-3"><div class="col-md-8"><label class="form-label" for="template-name">Nombre</label><input class="form-control" id="template-name" name="name" required maxlength="120" value="{{ old('name', $template->name) }}"></div><div class="col-md-4"><label for="template-active" class="form-label">Disponibilidad</label><select class="form-select" id="template-active" name="is_active"><option value="1" @selected(old('is_active', $template->is_active) == 1)>Activa</option><option value="0" @selected(old('is_active', $template->is_active) == 0)>Inactiva</option></select></div>
<div class="col-12"><label class="form-label" for="template-subject">Asunto</label><input class="form-control" id="template-subject" name="subject" required maxlength="191" value="{{ old('subject', $version?->subject) }}"></div>
<div class="col-12"><label class="form-label" for="template-editor">Mensaje</label>
<div class="d-flex flex-wrap gap-2 mb-2" id="template-toolbar"><button class="btn btn-palette-gray btn-sm" type="button" data-format="strong"><strong>Negrita</strong></button><button class="btn btn-palette-gray btn-sm" type="button" data-format="em"><em>Cursiva</em></button><button class="btn btn-palette-gray btn-sm" type="button" data-format="u">Subrayado</button><button class="btn btn-palette-gray btn-sm" type="button" data-format="h3">Título</button><button class="btn btn-palette-gray btn-sm" type="button" data-format="p">Párrafo</button><button class="btn btn-palette-gray btn-sm" type="button" data-format="ul">Lista</button></div>
<div contenteditable="true" role="textbox" aria-multiline="true" aria-label="Mensaje de la plantilla" id="template-editor" class="form-control email-template-editor">{!! app(\App\Services\RenderEmailTemplate::class)->sanitize(old('body', $version?->body ?? '<p></p>')) !!}</div>
<textarea name="body" id="template-body" hidden>{{ old('body', $version?->body) }}</textarea>
</div></div>
<div class="mt-3"><p>Insertar variable en el mensaje:</p><div class="d-flex flex-wrap gap-2">@foreach(\App\Services\RenderEmailTemplate::VARIABLES as $key=>$label)<button class="btn btn-palette-blue btn-sm" type="button" data-variable="{{ $key }}" title="{{ $label }}">{{ '{'.'{ '.$key.' }'.'}' }}</button>@endforeach</div><small class="text-muted">Las variables también se pueden escribir en el asunto. Se reemplazan con datos guardados al preparar el envío; no se ejecuta código.</small></div>
<div class="table-actions mt-4"><button class="btn btn-palette-green">{{ $template->exists ? 'Guardar nueva versión' : 'Crear plantilla' }}</button><a class="btn btn-palette-gray" href="{{ route('email-templates.index') }}">Cancelar</a></div>
</form>
@vite('resources/js/email-templates.js')
@endsection
