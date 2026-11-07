@extends('layouts.app')
@section('page-title',$profile->exists ? 'Editar perfil de simulación' : 'Nuevo perfil de simulación')
@section('page-description','Los cambios se aplican a las próximas ejecuciones; cada inicio conserva un snapshot independiente.')
@section('content')
@if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<div class="card shadow-sm"><div class="card-body p-4">
<form method="post" action="{{ $profile->exists ? route('claim-simulations.automatic.update',$profile) : route('claim-simulations.automatic.store') }}" data-simulation-profile>
@csrf @if($profile->exists) @method('put') @endif
<div class="row g-3">
<div class="col-md-6"><label for="name" class="form-label">Nombre</label><input id="name" name="name" class="form-control" maxlength="191" value="{{ old('name',$profile->name) }}" required></div>
<div class="col-md-6"><label for="credential" class="form-label">Aseguradora ficticia / credencial</label><select id="credential" name="credential_id" class="form-select" required>@foreach($credentials as $credential)<option value="{{ $credential->id }}" @selected(old('credential_id',$profile->credential_id)==$credential->id)>{{ $credential->company->name }} · {{ $credential->name }}</option>@endforeach</select></div>
@foreach(['scenario'=>['normal'=>'Operación normal','peak'=>'Hora pico','weather'=>'Evento climático','communication'=>'Problemas de comunicación'],'distribution'=>['exponential'=>'Intervalos variables (exponencial)','fixed'=>'Intervalos fijos'],'fault_type'=>['timeout'=>'Timeout simulado','transient'=>'Fallo transitorio simulado','http_error'=>'HTTP 503 simulado','lost_response'=>'Respuesta perdida después del envío real','duplicate'=>'Duplicado intencional con el mismo ID']] as $key=>$options)
<div class="col-md-4"><label for="{{ $key }}" class="form-label">{{ ['scenario'=>'Escenario','distribution'=>'Distribución temporal','fault_type'=>'Falla de laboratorio'][$key] }}</label><select id="{{ $key }}" name="configuration[{{ $key }}]" class="form-select">@foreach($options as $value=>$label)<option value="{{ $value }}" @selected(old('configuration.'.$key,$profile->configuration[$key])===$value)>{{ $label }}</option>@endforeach</select></div>
@endforeach
@foreach(['rate_per_hour'=>['Frecuencia promedio / hora',0.1,120,0.1],'duration_minutes'=>['Duración en minutos (vacío: indefinida)',1,10080,1],'max_per_hour'=>['Máximo de intentos / hora',1,120,1],'max_pending'=>['Máximo de eventos pendientes',1,50,1],'max_concurrency'=>['Máximo de envíos simultáneos por compañía',1,3,1],'daily_limit'=>['Límite diario de intentos (opcional)',1,2000,1],'seed'=>['Semilla (opcional)',0,2147483647,1],'peak_multiplier'=>['Multiplicador de pico / clima',1,10,0.1],'peak_minutes'=>['Minutos iniciales de pico / clima',1,1440,1],'fault_percent'=>['Eventos con falla (%)',0,50,1]] as $key=>$field)
<div class="col-md-4"><label for="{{ $key }}" class="form-label">{{ $field[0] }}</label><input id="{{ $key }}" type="number" name="configuration[{{ $key }}]" class="form-control" min="{{ $field[1] }}" max="{{ $field[2] }}" step="{{ $field[3] }}" value="{{ old('configuration.'.$key,$profile->configuration[$key] ?? '') }}" @required(!in_array($key,['duration_minutes','daily_limit','seed']))></div>
@endforeach
<div class="col-12"><h2 class="h6 mt-2">Distribución de motivos (ponderación relativa)</h2><p class="small text-secondary">Usá 0 para excluir un motivo. Los valores positivos se normalizan; no necesitan sumar 100. Clima usa Todo riesgo y describe granizo en las notas, sin inventar un catálogo nuevo.</p></div>
@foreach(config('claim_integrations.reasons') as $reason)<div class="col-md-3"><label for="reason-{{ $loop->index }}" class="form-label">{{ $reason }}</label><input id="reason-{{ $loop->index }}" name="configuration[reasons][{{ $reason }}]" type="number" min="0" max="100" class="form-control" value="{{ old('configuration.reasons.'.$reason,$profile->configuration['reasons'][$reason] ?? 0) }}" required></div>@endforeach
</div>
<div class="alert alert-info mt-3" data-simulation-preview>La frecuencia es un promedio; los límites pueden reducir el volumen. No se recuperan en ráfaga los intervalos perdidos. La duración incluye el tiempo pausado.</div>
<p class="small text-secondary">Las fallas están deshabilitadas por defecto y requieren CLAIM_SIMULATION_FAULTS_ENABLED. No se altera la API receptora.</p>
<label class="d-block mb-3"><input type="checkbox" name="enabled" value="1" @checked(old('enabled',$profile->enabled))> Perfil habilitado</label>
<button class="btn btn-primary" @disabled($credentials->isEmpty())>Guardar perfil</button><a class="btn btn-secondary ms-2" href="{{ route('claim-simulations.automatic.index') }}">Volver</a>
</form></div></div>
@push('scripts')<script src="{{ asset('assets/js/simulation-dashboard.js') }}" defer></script>@endpush
@endsection
