@extends('layouts.app')
@section('page-title', 'Gestionar inspección')
@section('content')
<div class="panel">
    <h2 class="h4">{{ $siniestro->claim_number }}</h2>
    <p>{{ $siniestro->company?->name }} · {{ $siniestro->license_plate }} · Fecha IP: {{ $siniestro->inspection_date?->format('d/m/Y') ?? 'Sin fecha' }}</p>
    <div class="mb-4">
        @if($siniestro->client)
            <a class="person-link" href="{{ route('clients.show', $siniestro->client_id) }}">{{ $siniestro->client->name ?: 'Ver asegurado' }}</a>
        @endif
    </div>
    @if($errors->any())<div class="alert alert-danger" role="alert">{{ $errors->first() }}</div>@endif
    <form action="{{ route('claims.update', $siniestro->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label class="form-label" for="inspection-status">Estado</label>
            <select class="form-select" id="inspection-status" name="status">
                @if(!auth()->user()->isRestrictedInspector() && !in_array(old('status', $siniestro->status), ['Peritando','IPgestion']))
                <option value="{{ old('status', $siniestro->status) }}" selected>{{ old('status', $siniestro->status) }}</option>
                @endif
                <option value="Peritando" @selected(old('status', $siniestro->status) === 'Peritando')>Peritando · pendiente de realizar</option>
                <option value="IPgestion" @selected(old('status', $siniestro->status) === 'IPgestion')>En gestión · inspección realizada</option>
            </select>
        </div>
        @include("siniestros.partials._repair_fields")
        <div class="mb-3">
            <label class="form-label" for="inspection-notes">Observaciones del inspector</label>
            <textarea class="form-control" id="inspection-notes" name="inspector_notes" rows="6">{{ old('inspector_notes', $siniestro->inspector_notes) }}</textarea>
        </div>
        <div class="mb-4">
            <label class="form-label" for="inspection-files">Adjuntar fotos o informe</label>
            <input class="form-control" id="inspection-files" type="file" name="urls[]" multiple accept=".pdf,.jpg,.jpeg,.png,.webp">
        </div>
        <div class="table-actions">
            @if(auth()->user()->canPlanInspectionRoutes() || auth()->user()->isRestrictedInspector())
            <a class="btn btn-palette-blue" href="{{ route('route-sheets.index', ['date' => $siniestro->inspection_date?->format('Y-m-d')]) }}">Ver hoja de ruta</a>
            @endif
            <button class="btn btn-primary" type="submit">Guardar gestión</button>
            <a class="btn btn-palette-gray" href="{{ route('claims.index') }}">Volver a mis inspecciones</a>
            <a class="btn btn-palette-blue" href="{{ route('claims.show', $siniestro->id) }}">Ver adjuntos</a>
        </div>
    </form>
</div>
@include("siniestros.partials._workshop_modal")
@vite("resources/js/inspection.js")
@endsection
