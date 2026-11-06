@extends('layouts.app')
@section('page-title', 'Asignar inspecciones')
@section('content')
<x-table-panel>
<table class="table align-middle w-100 app-data-table" data-standard-table>
            <thead><tr><th>Siniestro</th><th>Asegurado</th><th>Inspector</th><th>Fecha IP</th><th>Modalidad</th><th>Asignación</th></tr></thead>
            <tbody>
                @foreach($siniestros as $siniestro)
                    <tr>
                        <td>{{ $siniestro->claim_number }}</td>
                        @include('siniestros.partials._person_links')
                        <td>{{ $siniestro->inspection_date?->format('Y-m-d') }}</td>
                        <td>{{ $siniestro->inspection_mode }}</td>
                        <td>
                            @canany(['editar-siniestro', 'derivar-siniestro', 'ceo-siniestro'])
                                <form action="{{ route('claims.update', $siniestro->id) }}" method="POST" class="d-flex gap-2">
                                    @csrf
                                    @method('PUT')
                                    <select name="inspector_id" class="form-select form-select-sm" aria-label="Asignar inspector">
                                        <option value="">Sin asignar</option>
                                        @foreach($users as $inspector)
                                            <option value="{{ $inspector->id }}" @selected($siniestro->inspector_id == $inspector->id)>{{ $inspector->name }}</option>
                                        @endforeach
                                    </select>
                                    <button class="btn btn-primary btn-sm" type="submit">Guardar</button>
                                </form>
                            @else
                                <span class="text-muted">Sin permiso para asignar</span>
                            @endcanany
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
</x-table-panel>
@endsection
