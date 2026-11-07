@extends('layouts.app')
@section('page-title','Laboratorio de aseguradoras')
@section('page-description','Generá siniestros ficticios, revisá sus datos y enviá cada lote mediante la API de integración.')
@section('content')
@include('integrations._runtime')
<div class="mb-3"><a class="btn btn-primary" href="{{ route('claim-simulations.automatic.index') }}">Simulaciones automáticas</a></div>
<div class="alert alert-info">Entorno de simulación · Los envíos usan datos ficticios y compañías de laboratorio. No se realizan comunicaciones externas.</div>
@if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
<div class="card border-0 shadow-sm mb-4"><div class="card-body p-4">
<h2 class="h5 mb-3">Preparar un lote</h2>
<form method="post" action="{{ route('claim-simulations.store') }}">
@csrf
<div class="row g-3">
<div class="col-md-5"><label for="credential" class="form-label">Aseguradora ficticia</label><select id="credential" name="credential_id" class="form-select" required>
@forelse($credentials as $credential)<option value="{{ $credential->id }}" @selected(old('credential_id')==$credential->id)>{{ $credential->company->name }}</option>
@empty<option value="">Cargá la compañía demo para comenzar</option>@endforelse
</select></div>
<div class="col-md-5"><label for="reason" class="form-label">Motivo del siniestro</label><select id="reason" name="reason" class="form-select" required>
@foreach(config('claim_integrations.reasons') as $reason)<option value="{{ $reason }}" @selected(old('reason','Todo riesgo')===$reason)>{{ ['Todo riesgo'=>'Colisión / Todo riesgo','ampliacion'=>'Ampliación','actualizaciondevalores'=>'Actualización de valores','cotizarsincerrar'=>'Cotizar sin cerrar','robo'=>'Robo','incendio'=>'Incendio','posibleDT'=>'Posible destrucción total'][$reason] }}</option>@endforeach
</select></div>
<div class="col-md-2"><label for="quantity" class="form-label">Cantidad</label><input id="quantity" name="quantity" type="number" class="form-control" min="1" max="{{ config('claim_integrations.max_batch') }}" value="{{ old('quantity',1) }}" required></div>
<div class="col-md-4"><label for="seed" class="form-label">Semilla (opcional)</label><input id="seed" name="seed" type="number" min="0" max="2147483647" value="{{ old('seed') }}" class="form-control"><div class="form-text">Reproduce nombres y circunstancias; cada lote recibe IDs externos nuevos.</div></div>
</div><p class="small text-secondary mt-3">El dominio actual no contempla gravedad. Se usan los motivos, modalidades y datos del asegurado del módulo operativo.</p>
<button class="btn btn-primary" @disabled($credentials->isEmpty())>Generar y previsualizar</button>
</form></div></div>
<div class="card border-0 shadow-sm"><div class="card-body"><h2 class="h5">Historial de ejecuciones</h2>
<div class="table-responsive"><table class="table align-middle"><thead><tr><th>Lote</th><th>Compañía</th><th>Fecha</th><th>Cantidad</th><th>Estado</th><th></th></tr></thead><tbody>
@forelse($runs as $run)<tr><td>#{{ $run->id }}</td><td>{{ $run->credential->company->name }}</td><td>{{ $run->created_at->format('d/m/Y H:i') }}</td><td>{{ $run->items_count }}</td><td>{{ ['preview'=>'Pendiente de envío','sending'=>'Enviando','finished'=>'Finalizado'][$run->status] ?? $run->status }}</td><td><a href="{{ route('claim-simulations.show',$run) }}">Ver resultados</a></td></tr>
@empty<tr><td colspan="6" class="text-secondary">No hay ejecuciones todavía.</td></tr>@endforelse
</tbody></table></div>{{ $runs->links() }}</div></div>
@endsection
