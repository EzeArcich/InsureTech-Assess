@extends('layouts.app')
@section('page-title','Lote de simulación #'.$run->id)
@section('page-description',$run->credential->company->name.' · Semilla '.$run->seed)
@section('content')
<a href="{{ route('claim-simulations.index') }}" class="btn btn-outline-secondary mb-3">Volver al laboratorio</a>
@if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
@if($run->status==='preview')
<div class="card border-0 shadow-sm mb-4"><div class="card-body p-4"><h2 class="h5">Revisá los datos antes de enviar</h2><p>Los payloads de abajo serán enviados por HTTP. No se registran siniestros hasta confirmar el envío.</p>
<form method="post" action="{{ route('claim-simulations.send',$run) }}">@csrf
<label class="form-check mb-3"><input class="form-check-input" type="checkbox" name="confirmation" value="1" required><span class="form-check-label">Confirmo el envío de {{ $run->items->count() }} siniestro(s) ficticio(s).</span></label>
<button class="btn btn-primary">Enviar lote a la API</button></form></div></div>
@endif
<div class="row g-3 mb-4">
@foreach(['created'=>'Creados','duplicate'=>'Duplicados','rejected'=>'Rechazados','transport_error'=>'Sin confirmación'] as $state=>$label)
<div class="col-6 col-xl-3"><div class="card border-0 shadow-sm"><div class="card-body"><div class="text-secondary">{{ $label }}</div><strong class="fs-3">{{ $run->items->where('status',$state)->count() }}</strong></div></div></div>
@endforeach</div>
@foreach($run->items as $item)
<article class="card border-0 shadow-sm mb-3"><div class="card-body p-4"><div class="d-flex justify-content-between gap-3 flex-wrap"><h2 class="h6">Solicitud {{ $item->position }} · {{ $item->payload['claim_number'] }}</h2><span class="badge text-bg-secondary">{{ ['preview'=>'Pendiente','created'=>'Creado','duplicate'=>'Duplicado','rejected'=>'Rechazado','transport_error'=>'Sin confirmación'][$item->status] ?? $item->status }}{{ $item->http_status ? ' · HTTP '.$item->http_status : '' }}</span></div>
<details @if($run->items->count()===1) open @endif><summary class="my-3">Payload de entrada</summary><pre class="bg-light p-3 rounded overflow-auto">{{ json_encode($item->payload,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) }}</pre></details>
@if($item->response)<details open><summary class="my-3">Respuesta de la API</summary><pre class="bg-light p-3 rounded overflow-auto">{{ json_encode($item->response,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) }}</pre></details>@endif
@if(in_array($item->status,['created','duplicate']) && data_get($item->response,'data.id'))<a class="btn btn-outline-primary btn-sm" href="{{ route('claims.edit',data_get($item->response,'data.id')) }}">Abrir expediente</a>@endif
</div></article>
@endforeach
@endsection
