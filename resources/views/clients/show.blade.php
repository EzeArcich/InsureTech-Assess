@extends('layouts.app')
@section('page-title', 'Ficha del asegurado')
@section('content')
<div class="panel">
    <h2 class="h4 mb-4">{{ $client->name ?: 'Asegurado sin nombre' }}</h2>
    <dl class="row profile-details">
        <dt class="col-sm-3">Compañía</dt><dd class="col-sm-9">{{ $client->company?->name ?? 'Sin compañía' }}</dd>
        <dt class="col-sm-3">Email</dt><dd class="col-sm-9">{{ $client->email ?? 'Sin informar' }}</dd>
        <dt class="col-sm-3">Teléfono</dt><dd class="col-sm-9">{{ $client->phone ?? 'Sin informar' }}</dd>
        <dt class="col-sm-3">Dirección</dt><dd class="col-sm-9">{{ $client->address ?? 'Sin informar' }}</dd>
        <dt class="col-sm-3">Localidad</dt><dd class="col-sm-9">{{ $client->city ?? 'Sin informar' }}</dd>
    </dl>
    @if($client->claim)
        <hr>
        <h3 class="h6">Siniestro asociado</h3>
        <p>{{ $client->claim->claim_number }} · {{ $client->claim->license_plate }} · {{ $client->claim->status }}</p>
        @if($client->claim->inspector)
            <a class="person-link" href="{{ route('inspectors.show', $client->claim->inspector_id) }}">{{ $client->claim->inspector->name }}</a>
        @endif
    @endif
</div>
@endsection
