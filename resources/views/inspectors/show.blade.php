@extends('layouts.app')
@section('page-title', 'Ficha del inspector')
@section('content')
<div class="panel mb-4">
    <h2 class="h4 mb-4">{{ $inspector->name }}</h2>
    <dl class="row profile-details">
        <dt class="col-sm-3">Email</dt><dd class="col-sm-9">{{ $inspector->email ?? 'Sin informar' }}</dd>
        <dt class="col-sm-3">Teléfono</dt><dd class="col-sm-9">{{ $inspector->phone ?? 'Sin informar' }}</dd>
        <dt class="col-sm-3">Siniestros asignados</dt><dd class="col-sm-9">{{ $claims->count() }}</dd>
    </dl>
</div>
<x-table-panel>
    <x-slot:actions><h3 class="h5 me-auto">Siniestros del inspector</h3></x-slot:actions>
<table class="table align-middle w-100 app-data-table" data-standard-table>
            <thead><tr><th>Siniestro</th><th>Asegurado</th><th>Inspector</th><th>Estado</th></tr></thead>
            <tbody>
                @foreach($claims as $siniestro)
                    <tr><td>{{ $siniestro->claim_number }}</td>@include('siniestros.partials._person_links')<td>{{ $siniestro->status }}</td></tr>
                @endforeach
            </tbody>
        </table>
</x-table-panel>
@endsection
