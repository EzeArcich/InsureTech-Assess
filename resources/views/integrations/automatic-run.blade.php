@extends('layouts.app')
@section('page-title','Ejecución automática #'.$run->id)
@section('page-description',$run->credential->company->name.' · '.$run->profile->name)
@section('content')
@if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
<div class="card shadow-sm mb-4"><div class="card-body">
@if($run->last_error)<div class="alert alert-warning">{{ $run->last_error }}</div>@endif
<p><strong>Estado: <span data-run-status="{{ $run->id }}">{{ $run->status }}</span></strong> · Inicio: {{ $run->started_at?->format('d/m/Y H:i:s') }} · Fin previsto: {{ $run->ends_at?->format('d/m/Y H:i:s') ?? 'Indefinido' }} · Próximo disparo: <span data-run-next="{{ $run->id }}">{{ $run->next_dispatch_at?->format('d/m/Y H:i:s') ?? '—' }}</span></p>
<div class="d-flex gap-2 flex-wrap">
@foreach(['pause'=>'Pausar','resume'=>'Reanudar','stop'=>'Detener'] as $action=>$label)
@if(in_array($run->status,['pending','running','paused']) && ($action !== 'resume' || $run->status==='paused'))<form method="post" action="{{ route('claim-simulations.automatic.transition',$run) }}">@csrf<input type="hidden" name="action" value="{{ $action }}"><button class="btn {{ $action==='stop' ? 'btn-warning' : 'btn-primary' }}">{{ $label }}</button></form>@endif
@endforeach
<a class="btn btn-secondary" href="{{ route('claim-simulations.automatic.show',$run) }}">Actualizar</a><a class="btn btn-secondary" href="{{ route('claim-simulations.automatic.index') }}">Volver</a></div>
<details class="mt-3"><summary>Snapshot de configuración</summary><pre class="mt-2">{{ json_encode($run->configuration,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre></details>
<p class="small text-secondary mt-2 mb-0">Pausar o detener impide nuevos envíos. Las solicitudes HTTP que ya comenzaron pueden finalizar. Un resultado desconocido conserva el mismo ID para reintentar de forma idempotente.</p>
</div></div>
@include('integrations._metrics',['metricsUrl'=>route('claim-simulations.automatic.metrics',['run_id'=>$run->id])])
<div class="card shadow-sm"><div class="card-body"><h2 class="h5">Eventos recientes</h2><div class="table-responsive"><table class="table align-middle"><thead><tr><th>Evento / ID externo</th><th>Programado</th><th>Estado</th><th>Intentos</th><th>HTTP</th><th>Resultado</th></tr></thead><tbody>
@forelse($items as $item)<tr><td>#{{ $item->position }}<br><small>{{ $item->external_id }}</small></td><td>{{ $item->scheduled_at?->format('d/m H:i:s') }}</td><td>{{ $item->status }}</td><td>{{ $item->attempts }}</td><td>{{ $item->http_status ?? '—' }}</td><td>@if($item->claim_id)<a href="{{ route('claims.edit',$item->claim_id) }}">Abrir expediente</a>@else{{ $item->last_error ?? '—' }}@endif<details><summary>Payload e intentos</summary>@foreach($attempts[$item->id] ?? [] as $attempt)<p class="small">Intento {{ $attempt->number }} · {{ $attempt->outcome }} · HTTP {{ $attempt->http_status ?? '—' }} · {{ $attempt->started_at }}</p>@endforeach<pre>{{ json_encode($item->payload,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre></details></td></tr>
@empty<tr><td colspan="6">El Scheduler todavía no planificó eventos.</td></tr>@endforelse
</tbody></table></div>{{ $items->links() }}</div></div>
@endsection
