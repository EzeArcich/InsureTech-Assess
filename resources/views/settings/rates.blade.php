@extends('layouts.app')
@section('page-title', $title)
@section('page-description', 'Aplicá valores a todas las compañías o a una en particular.')
@section('content')
<a class="btn btn-palette-gray mb-3" href="{{ route('settings.rates') }}">Volver a Configuración</a>
@if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
<form class="panel mb-3" action="{{ route($applyRoute) }}" method="POST" id="company-rate-form" data-rates="{{ json_encode($currentRates) }}" data-fields="{{ json_encode(array_keys($fields)) }}">
@csrf @method('PUT')
<div class="row g-3 mb-3"><div class="col-md-6"><label class="form-label" for="rate-scope">Aplicar a</label><select class="form-select" name="scope" id="rate-scope"><option value="company" @selected(old('scope','company') === 'company')>Una compañía</option><option value="all" @selected(old('scope') === 'all')>Todas las compañías actuales</option></select></div>
<div class="col-md-6" id="rate-company-container"><label class="form-label" for="rate-company">Compañía</label><select class="form-select" name="company_id" id="rate-company"><option value="">Seleccionar…</option>@foreach($companies as $company)<option value="{{ $company->id }}" @selected(old('company_id') == $company->id)>{{ $company->name }}</option>@endforeach</select></div></div>
<p id="rate-all-warning" class="text-muted" hidden>Se reemplazarán estos valores en todas las compañías actuales. Los importes ya acordados en presupuestos y pericias cerradas se conservan.</p>
<div class="row g-3">@foreach($fields as $field=>$label)<div class="col-sm-6 col-lg-4"><label class="form-label" for="rate-{{ $field }}">{{ $label }}</label><input class="form-control" id="rate-{{ $field }}" name="{{ $field }}" type="number" min="0" max="9999999.99" step="0.01" required value="{{ old($field) }}"></div>@endforeach</div>
<button class="btn btn-palette-green mt-3">Guardar valores</button>
</form>
<x-table-panel><x-slot:actions><h2 class="h5 me-auto">Valores vigentes por compañía</h2></x-slot:actions><table class="table app-data-table w-100" data-standard-table><thead><tr><th>Compañía</th>@foreach($fields as $label)<th>{{ $label }}</th>@endforeach</tr></thead><tbody>@foreach($companies as $company)<tr><td>{{ $company->name }}</td>@foreach($fields as $field=>$label)<td>{{ $company->$relation ? number_format($company->$relation->$field, 2, ',', '.') : 'Sin configurar' }}</td>@endforeach</tr>@endforeach</tbody></table></x-table-panel>
@vite('resources/js/settings-rates.js')
@endsection
