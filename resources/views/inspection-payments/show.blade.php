@extends('layouts.app')
@section('page-title', 'Detalle de honorario')
@section('content')
<div class="panel"><h2 class="h4">{{ $payment->claim?->claim_number }}</h2>
<dl class="row"><dt class="col-sm-4">Compañía</dt><dd class="col-sm-8">{{ $payment->company?->name }}</dd><dt class="col-sm-4">Perito</dt><dd class="col-sm-8">{{ $payment->inspector?->name }}</dd><dt class="col-sm-4">Modalidad</dt><dd class="col-sm-8">{{ ['in_person'=>'Presencial','photos'=>'Fotos','video_call'=>'Videollamada'][$payment->modality] }}</dd><dt class="col-sm-4">Honorario acordado</dt><dd class="col-sm-8">{{ number_format($payment->amount, 2, ',', '.') }}</dd><dt class="col-sm-4">Fecha de cierre</dt><dd class="col-sm-8">{{ $payment->completed_at->format('d/m/Y H:i') }}</dd><dt class="col-sm-4">Pago</dt><dd class="col-sm-8">{{ $payment->paid_at?->format('d/m/Y H:i') ?? 'Pendiente de pago' }}</dd><dt class="col-sm-4">Patente</dt><dd class="col-sm-8">{{ $payment->claim?->license_plate }}</dd></dl>
<a class="btn btn-palette-gray" href="{{ route('inspection-payments.index') }}">Volver a honorarios</a>
</div>
@endsection
