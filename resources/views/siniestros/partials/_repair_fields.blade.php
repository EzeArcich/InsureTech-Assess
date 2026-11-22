@php
    $rates = $siniestro->company?->rates;
    $config = ['rates' => $rates?->only(['bodywork_day','bodywork_hour','mechanical_day','mechanical_hour','paint_panel']) ?? [], 'saved' => $siniestro->only(['bodywork_unit','bodywork_rate','mechanical_unit','mechanical_rate','paint_rate']), 'catalogPartsUrl' => route('claims.parts.catalog-search', $siniestro), 'catalogPartsSaveUrl' => route('claims.parts.catalog-update', $siniestro), 'workshopsUrl' => route('workshops.search')];
@endphp
<section id="repair-estimate" class="mb-4" data-config="{{ json_encode($config) }}">
<h3 class="h5">Mano de obra y pintura</h3>
<p class="text-muted">Ingresá cantidades decimales: por ejemplo, 0.5 para media unidad. Los importes se confirman al guardar.</p>
<div class="table-responsive"><table class="table"><thead><tr><th>Trabajo</th><th>Unidad</th><th>Cantidad</th><th>Tarifa</th><th>Subtotal</th></tr></thead><tbody>
@foreach(['bodywork'=>'Chapa','mechanical'=>'Mecánica','paint'=>'Pintura'] as $kind=>$label)
@php($quantity = $kind === 'paint' ? 'paint_panels' : $kind.'_units')
<tr data-labor="{{ $kind }}"><td>{{ $label }}</td><td>
@if($kind !== 'paint')<select class="form-select" name="{{ $kind }}_unit" data-unit aria-label="Unidad de {{ $label }}">@foreach(['hour'=>'Hora','day'=>'Día'] as $unit=>$unitLabel)<option value="{{ $unit }}" @selected(old($kind.'_unit', $siniestro->{$kind.'_unit'}) === $unit)>{{ $unitLabel }}</option>@endforeach</select>@else Paño @endif
</td><td><input class="form-control" type="number" name="{{ $quantity }}" value="{{ old($quantity, $siniestro->$quantity) }}" min="0" max="9999.99" step="0.01" required data-quantity aria-label="Cantidad de {{ $label }}"></td><td data-rate></td><td data-subtotal></td></tr>
@endforeach
</tbody><tfoot><tr><th colspan="4">Total mano de obra y pintura</th><td id="repair-total">{{ $siniestro->repair_total }}</td></tr></tfoot></table></div>
<h3 class="h5">Taller</h3>
<label for="workshop-search" class="form-label">Buscar por nombre o domicilio</label><input class="form-control mb-2" id="workshop-search" autocomplete="off"><div id="workshop-results" class="list-group mb-2" aria-live="polite"></div>
<select class="form-select" id="inspection-workshop" name="workshop_id" aria-label="Taller seleccionado"><option value="">Sin taller asignado</option>@if($selectedWorkshop)<option value="{{ $selectedWorkshop->id }}" selected>{{ $selectedWorkshop->name }} · {{ $selectedWorkshop->address }}</option>@endif</select>
<button type="button" class="btn btn-palette-blue my-2" data-bs-toggle="modal" data-bs-target="#workshop-modal">Agregar taller</button>
<div class="parts-management mt-4">
    <div class="parts-management__heading"><div><span class="parts-management__eyebrow">PERICIA · REPUESTOS</span><h3 class="h5 mb-1">Repuestos necesarios</h3><p class="mb-0">Seleccioná las piezas a revisar para esta unidad.</p></div><span class="parts-management__badge">{{ $siniestro->catalogParts->count() }} seleccionados</span></div>
    <div class="parts-management__vehicle"><i class="fas fa-car" aria-hidden="true"></i><div><small>VEHÍCULO ASOCIADO</small><strong>@if($siniestro->vehicle){{ $siniestro->vehicle->brand }} {{ $siniestro->vehicle->model }} · {{ $siniestro->vehicle->version }} {{ $siniestro->vehicle->year }}@else Sin vehículo asociado @endif</strong><span>{{ $siniestro->license_plate ?: 'Patente no informada' }} @if($siniestro->vehicle?->doors) · {{ $siniestro->vehicle->doors }} puertas @endif</span></div></div>
    @if($siniestro->vehicle?->catalog_configuration_code)
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#parts-modal"><i class="fas fa-tools me-2" aria-hidden="true"></i>Gestionar repuestos</button>
    <div class="modal fade" id="parts-modal" tabindex="-1" aria-labelledby="parts-modal-title" aria-hidden="true"><div class="modal-dialog modal-lg modal-dialog-scrollable"><div class="modal-content parts-modal"><div class="modal-header"><div><span class="parts-management__eyebrow">CATÁLOGO DE COLISIÓN</span><h2 class="modal-title fs-5" id="parts-modal-title">Seleccionar repuestos</h2></div><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button></div><div class="modal-body">
        <div class="alert alert-warning small">Catálogo de demostración: compatibilidad y equipamiento sin verificar. Validar antes de presupuestar o comprar.</div>
        <p class="fw-bold">{{ $siniestro->vehicle->brand }} {{ $siniestro->vehicle->model }} · {{ $siniestro->vehicle->version }} · {{ $siniestro->vehicle->year }}</p>
        <label class="form-label" for="catalog-parts-filter">Buscar por nombre o categoría</label><input class="form-control" id="catalog-parts-filter" type="search" placeholder="Ej. óptica, paragolpes..." autocomplete="off" aria-describedby="catalog-search-status">
        <p class="small text-muted mt-2" id="catalog-search-status" role="status" aria-live="polite">Escribí al menos 3 caracteres para buscar.</p>
        <div class="parts-modal__list" data-catalog-results></div>
        <button class="btn btn-outline-primary btn-sm mt-2" type="button" data-catalog-more hidden>Ver más resultados</button>
        <p class="text-danger small mt-2" data-catalog-save-error role="alert" hidden></p>
        <h3 class="h6 mt-4">Repuestos seleccionados</h3>
        <div class="parts-modal__list" data-catalog-selected>
        @foreach($selectedCatalogParts as $part)
            <label class="parts-modal__item" data-selected-code="{{ $part->code }}"><input class="form-check-input" type="checkbox" value="{{ $part->code }}" checked><span><strong>{{ $part->name }}</strong><small>{{ ucfirst($part->category) }} @if($part->equipment_dependent) · Depende del equipamiento @endif</small></span></label>
        @endforeach
        </div><p class="text-muted small mt-2" data-selected-empty>No seleccionaste repuestos todavía.</p>
    </div><div class="modal-footer"><span class="me-auto text-muted small" data-catalog-count></span><button type="button" class="btn btn-primary" data-catalog-save>Guardar</button></div></div></div></div>
    <p class="small text-muted mt-2 mb-0" data-catalog-save-feedback role="status" aria-live="polite">Los repuestos se guardan desde el modal.</p>
    @else
    <p class="text-muted mb-0">Asociá una configuración del catálogo desde la edición del siniestro para seleccionar repuestos.</p>
    @endif
    @if($siniestro->parts->isNotEmpty())<p class="small mt-2 mb-0">Hay {{ $siniestro->parts->count() }} repuestos anteriores conservados.</p>@endif
    <a class="d-inline-block mt-2" href="{{ route('claims.parts', $siniestro->id) }}">Ver repuestos guardados</a>
</div>
</section>
