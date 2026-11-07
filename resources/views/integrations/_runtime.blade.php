<div class="card shadow-sm mb-4" data-simulation-runtime="{{ route('claim-simulations.runtime.show') }}">
    <div class="card-body">
        <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">
            <div><h2 class="h5 mb-2">Motor del laboratorio</h2>
                <div class="d-flex gap-3 flex-wrap small">
                    @foreach(['queue'=>'Cola de envíos','scheduler'=>'Planificador','receiver'=>'API local'] as $key=>$label)
                        <span>{{ $label }}: <strong data-runtime-service="{{ $key }}">Consultando…</strong></span>
                    @endforeach
                </div>
            </div>
            <form method="post" action="{{ route('claim-simulations.runtime.store') }}">
                @csrf
                <button class="btn btn-primary" data-runtime-start>Encender motor</button>
            </form>
        </div>
        <p class="small text-secondary mt-3 mb-1">Después de prender la PC, usá este botón. Los procesos siguen trabajando al cerrar el navegador. Las ejecuciones activas retoman su procesamiento; no se crean perfiles ni ejecuciones nuevos.</p>
        <p class="small text-secondary mb-0" data-runtime-message role="status">Consultando disponibilidad del inicio local…</p>
    </div>
</div>
@pushOnce('scripts')<script src="{{ asset('assets/js/simulation-runtime.js') }}" defer></script>@endPushOnce
