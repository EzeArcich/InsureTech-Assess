@extends('layouts.app')
@section('page-title', 'Hoja de ruta de inspecciones')
@section('page-description', 'Agenda por perito y día. El mapa muestra únicamente inspecciones presenciales con ubicación guardada. Buscá el domicilio para encontrar y confirmar su punto.')
@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
@endpush
@section('content')
@if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
<form class="panel mb-3 d-flex flex-wrap gap-3 align-items-end" method="GET" action="{{ route('route-sheets.index') }}">
<div><label class="form-label" for="route-date">Día</label><input class="form-control" type="date" id="route-date" name="date" value="{{ $date }}" required></div>
@if(!auth()->user()->isRestrictedInspector())<div><label class="form-label" for="route-inspector">Perito</label><select class="form-select" name="inspector_id" id="route-inspector"><option value="">Todos los peritos</option>@foreach($inspectors as $inspector)<option value="{{ $inspector->id }}" @selected((string)$inspectorId === (string)$inspector->id)>{{ $inspector->name }}</option>@endforeach</select></div>@endif
<button class="btn btn-palette-green">Ver agenda</button>
</form>
<div class="panel mb-3">
<h2 class="h5">Mapa del {{ \Illuminate\Support\Carbon::parse($date)->format('d/m/Y') }}</h2>
<p>{{ $assignments->count() }} inspecciones asignadas · {{ $points->count() }} puntos presenciales confirmados.</p>
<div id="inspection-map" class="inspection-map" data-points="{{ $points->toJson() }}" data-tiles="{{ config('services.inspection_map.tile_url') }}"></div>
<p id="map-feedback" class="text-muted mt-2" role="status">Seleccioná «Ubicar» para marcar o corregir un domicilio con un clic en el mapa.</p>
<form id="map-location-form" method="POST" class="mt-3" hidden>@csrf @method('PUT')
<h3 class="h6" id="map-location-title"></h3>
<div class="mb-3"><button type="button" class="btn btn-palette-blue" id="geocode-address">Buscar domicilio</button><span class="text-muted ms-2">Incluí calle, altura y localidad.</span><div id="geocode-results" class="list-group mt-2" aria-live="polite"></div><small class="text-muted">Búsqueda puntual por Nominatim / OpenStreetMap. No incluyas nombres ni información personal en el domicilio.</small></div><div class="row g-2">
<div class="col-md-6"><label class="form-label" for="map-address">Domicilio de la inspección</label><input class="form-control" id="map-address" name="inspection_address" required maxlength="255"></div>
<div class="col-md-3"><label class="form-label" for="map-latitude">Latitud</label><input class="form-control" id="map-latitude" name="inspection_latitude" type="number" step="any" min="-90" max="90" required></div>
<div class="col-md-3"><label class="form-label" for="map-longitude">Longitud</label><input class="form-control" id="map-longitude" name="inspection_longitude" type="number" step="any" min="-180" max="180" required></div>
</div><button class="btn btn-palette-green mt-2">Confirmar ubicación</button><button class="btn btn-palette-gray mt-2" id="cancel-location" type="button">Cancelar</button>
</form></div>
<x-table-panel>
<x-slot:actions><h2 class="h5 me-auto">Inspecciones del día</h2></x-slot:actions>
<table class="table w-100 app-data-table" data-standard-table><thead><tr><th>Siniestro</th><th>Perito</th><th>Horario</th><th>Modalidad</th><th>Domicilio</th><th>Ubicación</th><th>Acciones</th></tr></thead><tbody>
@foreach($assignments as $claim)
<tr><td><a href="{{ route('claims.show', $claim->id) }}">{{ $claim->claim_number }}</a></td><td>{{ $claim->inspector?->name }}</td><td>{{ $claim->inspection_time ?: 'Sin horario' }}</td><td>{{ $claim->inspection_mode }}</td><td>{{ $claim->inspection_address ?: ($claim->workshop ? $claim->workshop->address.' · '.$claim->workshop->city : $claim->address.' · '.$claim->city) }}</td><td>{{ $claim->inspectionModality() !== 'in_person' ? 'Remota · fuera del mapa' : ($claim->inspection_latitude !== null && $claim->inspection_longitude !== null ? 'Confirmada' : 'Sin ubicar') }}</td><td><div class="table-actions">
@if($claim->inspectionModality() === 'in_person')<button class="btn btn-palette-blue btn-sm" type="button" data-locate data-geocode-url="{{ route('route-sheets.geocode', $claim) }}" data-url="{{ route('route-sheets.location', $claim) }}" data-number="{{ $claim->claim_number }}" data-address="{{ $claim->inspection_address ?: ($claim->workshop ? $claim->workshop->address.' · '.$claim->workshop->city : $claim->address.' · '.$claim->city) }}" data-lat="{{ $claim->inspection_latitude }}" data-lng="{{ $claim->inspection_longitude }}">{{ $claim->inspection_latitude !== null ? 'Revisar ubicación' : 'Buscar ubicación' }}</button>@endif
@if(auth()->user()->canPlanInspectionRoutes())<button type="button" class="btn btn-palette-yellow btn-sm" data-assign data-url="{{ route('route-sheets.assign', $claim) }}" data-number="{{ $claim->claim_number }}" data-inspector="{{ $claim->inspector_id }}" data-date="{{ $date }}" data-time="{{ $claim->inspection_time }}">Reasignar</button>@endif
</div></td></tr>
@endforeach
</tbody></table></x-table-panel>
@if(auth()->user()->canPlanInspectionRoutes())
<div class="panel mt-3"><h2 class="h5">Asignar perito</h2><p id="assignment-title">Seleccioná una inspección coordinada o una ya asignada.</p>
<form id="route-assignment-form" method="POST" hidden>@csrf @method('PUT')
<div class="row g-3"><div class="col-md-5"><label for="assignment-inspector" class="form-label">Perito</label><select id="assignment-inspector" class="form-select" name="inspector_id" required><option value="">Seleccionar…</option>@foreach($inspectors as $inspector)<option value="{{ $inspector->id }}">{{ $inspector->name }}</option>@endforeach</select></div><div class="col-md-4"><label class="form-label" for="assignment-date">Día</label><input class="form-control" id="assignment-date" name="inspection_date" type="date" required value="{{ $date }}"></div><div class="col-md-3"><label class="form-label" for="assignment-time">Hora</label><input class="form-control" id="assignment-time" name="inspection_time" type="time"></div></div><button class="btn btn-palette-green mt-3">Asignar y ver en agenda</button></form></div>
<x-table-panel class="mt-3"><x-slot:actions><h2 class="h5 me-auto">Coordinadas pendientes de asignación</h2></x-slot:actions>
<table class="table app-data-table w-100" data-standard-table><thead><tr><th>Siniestro</th><th>Compañía</th><th>Asegurado</th><th>Modalidad</th><th>Fecha prevista</th><th>Acciones</th></tr></thead><tbody>@foreach($candidates as $claim)<tr><td><a href="{{ route('claims.show', $claim) }}">{{ $claim->claim_number }}</a></td><td>{{ $claim->company?->name }}</td><td>{{ $claim->client_name }}</td><td>{{ $claim->inspection_mode }}</td><td>{{ $claim->inspection_date?->format('d/m/Y') }}</td><td><button class="btn btn-palette-green btn-sm" type="button" data-assign data-url="{{ route('route-sheets.assign', $claim) }}" data-number="{{ $claim->claim_number }}" data-date="{{ $claim->inspection_date?->format('Y-m-d') ?: $date }}" data-inspector="{{ $claim->inspector_id }}" data-time="{{ $claim->inspection_time }}">Asignar</button></td></tr>@endforeach</tbody></table>
</x-table-panel>
@endif
@endsection
@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
@vite('resources/js/route-sheets.js')
@endpush
