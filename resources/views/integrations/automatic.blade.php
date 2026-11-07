@extends('layouts.app')
@section('page-title','Simulaciones automáticas')
@section('page-description','Perfiles reutilizables y ejecuciones persistentes de aseguradoras ficticias.')
@section('content')
@include('integrations._runtime')
<div class="alert alert-info">Laboratorio local · El Scheduler planifica y los workers envían mediante la API. No hace falta mantener esta página abierta.</div>
@if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
<div class="d-flex gap-2 mb-3"><a href="{{ route('claim-simulations.index') }}" class="btn btn-secondary">Lotes manuales</a><a href="{{ route('claim-simulations.automatic.create') }}" class="btn btn-primary">Nuevo perfil</a></div>
<form method="get" class="mb-3"><label for="company" class="form-label">Métricas y perfiles de la aseguradora</label><div class="d-flex gap-2"><select id="company" name="company_id" class="form-select"><option value="">Todas las aseguradoras</option>@foreach($credentials->unique('company_id') as $credential)<option value="{{ $credential->company_id }}" @selected($companyId===$credential->company_id)>{{ $credential->company->name }}</option>@endforeach</select><button class="btn btn-primary">Filtrar</button></div></form>
@include('integrations._metrics',['metricsUrl'=>route('claim-simulations.automatic.metrics',['company_id'=>$companyId])])
<div class="card shadow-sm mb-4"><div class="card-body"><h2 class="h5">Perfiles</h2><div class="table-responsive"><table class="table align-middle"><thead><tr><th>Perfil</th><th>Aseguradora</th><th>Escenario / frecuencia</th><th>Duración</th><th>Estado</th><th>Acciones</th></tr></thead><tbody>
@forelse($profiles as $profile)<tr><td>{{ $profile->name }}</td><td>{{ $profile->credential->company->name }}</td><td>{{ $profile->configuration['scenario'] }} · {{ $profile->configuration['rate_per_hour'] }}/h</td><td>{{ $profile->configuration['duration_minutes'] ? $profile->configuration['duration_minutes'].' min' : 'Indefinida' }}</td><td>{{ $profile->enabled ? 'Habilitado' : 'Deshabilitado' }}</td><td><div class="d-flex gap-2 flex-wrap"><a class="btn btn-sm btn-secondary" href="{{ route('claim-simulations.automatic.edit',$profile) }}">Editar</a><form method="post" action="{{ route('claim-simulations.automatic.duplicate',$profile) }}">@csrf<button class="btn btn-sm btn-secondary">Duplicar</button></form></div>
@if($profile->enabled)<form method="post" class="mt-2" action="{{ route('claim-simulations.automatic.start',$profile) }}">@csrf<label class="small"><input type="checkbox" name="confirmation" value="1" required> Confirmo el envío automático de datos ficticios</label><button class="btn btn-sm btn-primary ms-2">Iniciar</button></form>@endif</td></tr>
@empty<tr><td colspan="6">Creá un perfil para comenzar.</td></tr>@endforelse
</tbody></table></div>{{ $profiles->links() }}</div></div>
<div class="card shadow-sm"><div class="card-body"><h2 class="h5">Ejecuciones e historial</h2><div class="table-responsive"><table class="table align-middle"><thead><tr><th>Ejecución</th><th>Aseguradora / perfil</th><th>Estado</th><th>Próximo disparo</th><th>Eventos</th><th></th></tr></thead><tbody>
@forelse($runs as $run)<tr><td>#{{ $run->id }}</td><td>{{ $run->credential->company->name }} / {{ $run->profile->name }}</td><td data-run-status="{{ $run->id }}">{{ $run->status }}</td><td data-run-next="{{ $run->id }}">{{ $run->next_dispatch_at?->format('d/m/Y H:i:s') ?? '—' }}</td><td>{{ $run->items_count }}</td><td><a href="{{ route('claim-simulations.automatic.show',$run) }}">Ver y controlar</a></td></tr>
@empty<tr><td colspan="6">No hay ejecuciones.</td></tr>@endforelse
</tbody></table></div>{{ $runs->links() }}<p class="small text-secondary">Las métricas se actualizan cada 15 segundos. El historial se consulta al abrir cada ejecución.</p></div></div>
@endsection
