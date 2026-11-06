@extends('layouts.app')
@section('page-title', 'Repuestos del siniestro')
@section('content')
<div class="panel mb-3"><h2 class="h5">{{ $claim->claim_number }}</h2>
@if($claim->workshop)<p>Taller: {{ $claim->workshop->name }} · {{ $claim->workshop->address }} · {{ $claim->workshop->city }}</p><p>Teléfono: {{ $claim->workshop->phone ?: 'Sin informar' }} · Horarios: {{ $claim->workshop->opening_hours ?: 'Sin informar' }}</p>@endif
<a class="btn btn-palette-gray" href="{{ route('claims.inspection', $claim) }}">Volver a la gestión</a></div>
<x-table-panel><table class="table app-data-table w-100" data-standard-table><thead><tr><th>Repuesto solicitado</th></tr></thead><tbody>@foreach($claim->parts as $part)<tr><td>{{ $part->name }}</td></tr>@endforeach</tbody></table></x-table-panel>
@endsection
