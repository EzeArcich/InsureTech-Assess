@extends('layouts.app')
@section('page-title', 'Configuración')
@section('page-description', 'Administración de valores acordados con las compañías.')
@section('content')
<div class="row g-3">
<div class="col-lg-6"><section class="panel h-100"><h2 class="h4">Chapa, pintura y mecánica</h2><p>Valores de reparación: día y hora de chapa y mecánica, y paño de pintura.</p><a class="btn btn-palette-green" href="{{ route('settings.repair-rates') }}">Configurar reparación</a></section></div>
<div class="col-lg-6"><section class="panel h-100"><h2 class="h4">Inspecciones</h2><p>Honorarios por pericia presencial, por fotos y por videollamada.</p><a class="btn btn-palette-blue" href="{{ route('settings.inspection-rates') }}">Configurar inspecciones</a></section></div>
</div>
@endsection
