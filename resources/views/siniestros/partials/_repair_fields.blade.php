@php
    $rates = $siniestro->company?->rates;
    $config = ['rates' => $rates?->only(['bodywork_day','bodywork_hour','mechanical_day','mechanical_hour','paint_panel']) ?? [], 'saved' => $siniestro->only(['bodywork_unit','bodywork_rate','mechanical_unit','mechanical_rate','paint_rate']), 'partsUrl' => route('parts.search'), 'workshopsUrl' => route('workshops.search')];
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
<h3 class="h5 mt-3">Repuestos necesarios</h3><input type="hidden" name="parts_present" value="1">
<select class="form-select" id="inspection-parts" name="part_names[]" multiple aria-label="Repuestos necesarios">@foreach(old('part_names', $siniestro->parts->pluck('name')->all()) as $name)<option value="{{ $name }}" selected>{{ $name }}</option>@endforeach</select>
<p class="mt-2"><a href="{{ route('claims.parts', $siniestro->id) }}">Ver listado guardado de repuestos</a></p>
</section>
