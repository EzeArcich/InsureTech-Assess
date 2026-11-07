<div class="row g-3 mb-4" @isset($metricsUrl) data-simulation-metrics="{{ $metricsUrl }}" @endisset>
@foreach(['active'=>'Ejecuciones activas','sent'=>'Intentos de envío','success'=>'Siniestros confirmados','errors'=>'Eventos fallidos','retries'=>'Reintentos','pending'=>'Eventos pendientes','success_rate'=>'Éxito (%)','effective_rate'=>'Intentos / hora efectiva'] as $key=>$label)
<div class="col-6 col-lg-3"><div class="card shadow-sm h-100"><div class="card-body"><div class="small text-secondary">{{ $label }}</div><strong class="h4" data-metric="{{ $key }}">{{ $metrics[$key] }}</strong></div></div></div>
@endforeach
</div>
@isset($metricsUrl)
@pushOnce('scripts')<script src="{{ asset('assets/js/simulation-dashboard.js') }}" defer></script>@endPushOnce
@endisset
