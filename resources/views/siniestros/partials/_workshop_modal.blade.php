<div class="modal fade" id="workshop-modal" tabindex="-1" aria-labelledby="workshop-modal-title" aria-hidden="true"><div class="modal-dialog"><div class="modal-content">
<form id="inspection-workshop-form" action="{{ route('claims.workshops.store', $siniestro->id) }}" method="POST">@csrf
<div class="modal-header"><h2 class="modal-title h5" id="workshop-modal-title">Agregar taller</h2><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button></div>
<div class="modal-body"><div id="workshop-errors" class="text-danger mb-2" role="alert"></div>
@foreach(['name'=>'Nombre','address'=>'Domicilio','city'=>'Localidad','phone'=>'Teléfono','email'=>'Email','opening_hours'=>'Horarios'] as $field=>$label)
<div class="mb-2"><label class="form-label" for="new-workshop-{{ $field }}">{{ $label }}</label><input class="form-control" id="new-workshop-{{ $field }}" name="{{ $field }}" maxlength="{{ $field === 'opening_hours' ? 1000 : 191 }}" @if(in_array($field,['name','address'])) required @endif type="{{ $field === 'email' ? 'email' : 'text' }}"></div>
@endforeach
<p class="text-muted">Los talleres agregados por el inspector quedan como no homologados.</p><div id="workshop-duplicates" class="list-group" aria-live="polite"></div>
</div><div class="modal-footer"><button class="btn btn-palette-gray" type="button" data-bs-dismiss="modal">Cancelar</button><button class="btn btn-palette-green" type="submit">Guardar y seleccionar</button></div>
</form></div></div></div>
